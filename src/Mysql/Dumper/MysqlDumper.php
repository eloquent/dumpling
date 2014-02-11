<?php

/*
 * This file is part of the Dumpling package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Dumpling\Mysql\Dumper;

use Eloquent\Dumpling\Console\DumplingApplication;
use Eloquent\Dumpling\Mysql\Client\MysqlClientInterface;
use Eloquent\Dumpling\Process\ProcessFactory;
use Eloquent\Dumpling\Process\ProcessFactoryInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Dumps the structure and contents of MySQL databases and tables.
 */
class MysqlDumper implements MysqlDumperInterface
{
    /**
     * Construct a new MySQL dumper.
     *
     * @param MysqlClientInterface         $client           The MySQL client to use.
     * @param ExecutableFinder|null        $executableFinder The executable finder to use.
     * @param ProcessFactoryInterface|null $processFactory   The process factory to use.
     */
    public function __construct(
        MysqlClientInterface $client,
        ExecutableFinder $executableFinder = null,
        ProcessFactoryInterface $processFactory = null
    ) {
        if (null === $executableFinder) {
            $executableFinder = new ExecutableFinder;
        }
        if (null === $processFactory) {
            $processFactory = new ProcessFactory;
        }

        $this->client = $client;
        $this->executableFinder = $executableFinder;
        $this->processFactory = $processFactory;
    }

    /**
     * Get the MySQL client.
     *
     * @return MysqlClientInterface The MySQL client.
     */
    public function client()
    {
        return $this->client;
    }

    /**
     * Get the executable finder.
     *
     * @return ExecutableFinder The executable finder.
     */
    public function executableFinder()
    {
        return $this->executableFinder;
    }

    /**
     * Get the process factory.
     *
     * @return ProcessFactoryInterface The process factory.
     */
    public function processFactory()
    {
        return $this->processFactory;
    }

    /**
     * Dump MySQL information to a callback.
     *
     * The callback accepts two arguments. The first is the type of output,
     * which will be either Process::OUT, or Process::ERR, for standard output
     * and error output respectively. The second is the output itself.
     *
     * @param callable           $callback         The callback to pass output to.
     * @param boolean|null       $excludeData      True if data should be excluded.
     * @param array<string>|null $databases        The list of databases to dump.
     * @param array<string>|null $tables           The list of tables to dump.
     * @param array<string>|null $excludeDatabases The list of databases to exclude from the dump.
     * @param array<string>|null $excludeTables    The list of tables to exclude from the dump.
     * @param boolean|null       $useLocks         True if tables should be locked before dumping.
     * @param boolean|null       $useTransactions  True if transactions should be used when dumping.
     *
     * @throws Exception\NoDatabasesException      If there are no databases to dump.
     * @throws Exception\UnqualifiedTableException If an unqualified table name was supplied.
     * @throws Exception\DumpFailedException       If the dump failed.
     */
    public function dump(
        $callback,
        $excludeData = null,
        array $databases = null,
        array $tables = null,
        array $excludeDatabases = null,
        array $excludeTables = null,
        $useLocks = null,
        $useTransactions = null
    ) {
        if (null === $excludeData) {
            $excludeData = false;
        }
        if (null === $useLocks) {
            $useLocks = true;
        }
        if (null === $useTransactions) {
            $useTransactions = false;
        }

        $arguments = array(
            $this->executableFinder->find('mysqldump', 'mysqldump'),
            '--routines',
            '--skip-extended-insert',
            '--order-by-primary',
            '--hex-blob',
            '--host',
            $this->client()->host(),
            '--port',
            strval($this->client()->port()),
            '--user',
            $this->client()->username(),
            '--protocol',
            'TCP',
        );

        if (null !== $this->client()->password()) {
            $arguments[] = '--password=' . $this->client()->password();
        }

        if ($excludeData) {
            $arguments[] = '--no-data';
        }
        if (!$useLocks) {
            $arguments[] = '--skip-lock-tables';
        }
        if ($useTransactions) {
            $arguments[] = '--single-transaction';
        }

        list($entities, $isMultiDump) = $this->normalizeEntities(
            $databases,
            $tables,
            $excludeDatabases,
            $excludeTables
        );

        $this->writePrimaryHeader($callback);
        if ($isMultiDump) {
            $this->dumpMulti($callback, $arguments, $entities);
        } else {
            $this->dumpSingle($callback, $arguments, $entities);
        }
    }

    /**
     * Dump MySQL information to a stream.
     *
     * @param stream             $output           The output stream to write to.
     * @param boolean|null       $excludeData      True if data should be excluded.
     * @param array<string>|null $databases        The list of databases to dump.
     * @param array<string>|null $tables           The list of tables to dump.
     * @param array<string>|null $excludeDatabases The list of databases to exclude from the dump.
     * @param array<string>|null $excludeTables    The list of tables to exclude from the dump.
     * @param boolean|null       $useLocks         True if tables should be locked before dumping.
     * @param boolean|null       $useTransactions  True if transactions should be used when dumping.
     *
     * @throws Exception\NoDatabasesException      If there are no databases to dump.
     * @throws Exception\UnqualifiedTableException If an unqualified table name was supplied.
     * @throws Exception\DumpFailedException       If the dump failed.
     */
    public function dumpToStream(
        $output,
        $excludeData = null,
        array $databases = null,
        array $tables = null,
        array $excludeDatabases = null,
        array $excludeTables = null,
        $useLocks = null,
        $useTransactions = null
    ) {
        $data = '';
        $this->dump(
            function ($type, $buffer) use ($output) {
                if (Process::ERR !== $type) {
                    fwrite($output, $buffer);
                }
            },
            $excludeData,
            $databases,
            $tables,
            $excludeDatabases,
            $excludeTables,
            $useLocks,
            $useTransactions
        );

        return $data;
    }

    /**
     * Dump MySQL information to a string.
     *
     * @param boolean|null       $excludeData      True if data should be excluded.
     * @param array<string>|null $databases        The list of databases to dump.
     * @param array<string>|null $tables           The list of tables to dump.
     * @param array<string>|null $excludeDatabases The list of databases to exclude from the dump.
     * @param array<string>|null $excludeTables    The list of tables to exclude from the dump.
     * @param boolean|null       $useLocks         True if tables should be locked before dumping.
     * @param boolean|null       $useTransactions  True if transactions should be used when dumping.
     *
     * @return string                              The MySQL information.
     * @throws Exception\NoDatabasesException      If there are no databases to dump.
     * @throws Exception\UnqualifiedTableException If an unqualified table name was supplied.
     * @throws Exception\DumpFailedException       If the dump failed.
     */
    public function dumpToString(
        $excludeData = null,
        array $databases = null,
        array $tables = null,
        array $excludeDatabases = null,
        array $excludeTables = null,
        $useLocks = null,
        $useTransactions = null
    ) {
        $data = '';
        $this->dump(
            function ($type, $buffer) use (&$data) {
                if (Process::ERR !== $type) {
                    $data .= $buffer;
                }
            },
            $excludeData,
            $databases,
            $tables,
            $excludeDatabases,
            $excludeTables,
            $useLocks,
            $useTransactions
        );

        return $data;
    }

    /**
     * Dump MySQL information to the console.
     *
     * @param OutputInterface    $output           The console output to use.
     * @param boolean|null       $excludeData      True if data should be excluded.
     * @param array<string>|null $databases        The list of databases to dump.
     * @param array<string>|null $tables           The list of tables to dump.
     * @param array<string>|null $excludeDatabases The list of databases to exclude from the dump.
     * @param array<string>|null $excludeTables    The list of tables to exclude from the dump.
     * @param boolean|null       $useLocks         True if tables should be locked before dumping.
     * @param boolean|null       $useTransactions  True if transactions should be used when dumping.
     *
     * @throws Exception\NoDatabasesException      If there are no databases to dump.
     * @throws Exception\UnqualifiedTableException If an unqualified table name was supplied.
     * @throws Exception\DumpFailedException       If the dump failed.
     */
    public function dumpToConsole(
        OutputInterface $output,
        $excludeData = null,
        array $databases = null,
        array $tables = null,
        array $excludeDatabases = null,
        array $excludeTables = null,
        $useLocks = null,
        $useTransactions = null
    ) {
        return $this->dump(
            function ($type, $buffer) use ($output) {
                if (Process::ERR !== $type) {
                    $output->write($buffer);
                }
            },
            $excludeData,
            $databases,
            $tables,
            $excludeDatabases,
            $excludeTables,
            $useLocks,
            $useTransactions
        );
    }

    /**
     * Perform a dump using multiple commands to work around some technical
     * limitations of mysqldump.
     *
     * @param callable                    $callback  The callback to pass output to.
     * @param array<string>               $arguments The mysqldump arguments.
     * @param array<string,array<string>> $entities  The normalized entities to dump.
     *
     * @throws Exception\DumpFailedException If any dump fails.
     */
    protected function dumpMulti($callback, array $arguments, array $entities)
    {
        foreach ($entities as $database => $tableConfiguration) {
            $this->dumpSingle(
                $callback,
                $arguments,
                array($database => $tableConfiguration)
            );
        }
    }

    /**
     * Perform a dump that can be performed with a single mysqldump execution.
     *
     * @param callable                    $callback  The callback to pass output to.
     * @param array<string>               $arguments The mysqldump arguments.
     * @param array<string,array<string>> $entities  The normalized entities to dump.
     *
     * @throws Exception\DumpFailedException If the dump failed.
     */
    protected function dumpSingle($callback, array $arguments, array $entities)
    {
        if (count($entities) > 0) {
            $arguments[] = '--databases';

            foreach ($entities as $database => $tableConfiguration) {
                $arguments[] = $database;

                if (count($tableConfiguration['include']) > 0) {
                    $arguments[] = '--tables';

                    foreach ($tableConfiguration['include'] as $table) {
                        $arguments[] = $table;
                    }
                }
            }

            foreach ($entities as $database => $tableConfiguration) {
                foreach ($tableConfiguration['exclude'] as $table) {
                    $arguments[] = '--ignore-table';
                    $arguments[] = sprintf('%s.%s', $database, $table);
                }
            }
        }

        $this->writeCommandHeader($callback, $arguments);
        $process = $this->processFactory()->create($arguments);

        $exitCode = $process->run($callback);

        if (!$process->isSuccessful()) {
            throw new Exception\DumpFailedException(
                new Exception\MysqldumpException(
                    $process->getErrorOutput(),
                    $exitCode
                )
            );
        }
    }

    /**
     * Normalize the supplied entity inclusions and exclusions.
     *
     * @param array<string> $databases        The databases to include.
     * @param array<string> $tables           The tables to include.
     * @param array<string> $excludeDatabases The databases to exclude.
     * @param array<string> $excludeTables    The tables to exclude.
     *
     * @return array<string,array<string>>         The normalized entities.
     * @throws Exception\NoDatabasesException      If there are no databases to dump.
     * @throws Exception\UnqualifiedTableException If an unqualified table name was supplied.
     */
    protected function normalizeEntities(
        array $databases = null,
        array $tables = null,
        array $excludeDatabases = null,
        array $excludeTables = null
    ) {
        if (null === $databases) {
            $databases = array();
        }
        if (null === $tables) {
            $tables = array();
        }
        if (null === $excludeDatabases) {
            $excludeDatabases = array();
        }
        if (null === $excludeTables) {
            $excludeTables = array();
        }

        $databasesSpecified = count($databases) > 0;
        $tables = $this->normalizeArray($tables);
        $tablesSpecified = count($tables) > 0;

        if (!$tablesSpecified && !$databasesSpecified) {
            $databases = $this->client()->listDatabases();
        }
        $databases = $this->normalizeArray($databases);
        if (!$tablesSpecified && count($databases) < 1) {
            throw new Exception\NoDatabasesException;
        }

        $excludeDatabases = $this->normalizeArray(
            array_merge(
                array(
                    'mysql',
                    'information_schema',
                    'performance_schema',
                ),
                $excludeDatabases
            )
        );
        $excludeTables = $this->normalizeArray($excludeTables);

        $primaryDatabase = null;
        if (array_key_exists(0, $databases)) {
            $primaryDatabase = $databases[0];
        }
        $tables = $this->normalizeTables($tables, $primaryDatabase);
        $excludeTables = $this->normalizeTables(
            $excludeTables,
            $primaryDatabase
        );

        $entities = array();
        $tableIncludeCount = 0;
        foreach ($databases as $database) {
            if (
                !$databasesSpecified &&
                in_array($database, $excludeDatabases, true)
            ) {
                continue;
            }

            $entities[$database] = array(
                'include' => array(),
                'exclude' => array(),
            );
        }
        foreach ($tables as $database => $databaseTables) {
            if (count($databaseTables) > 0) {
                ++$tableIncludeCount;
            }

            $entities[$database]['include'] = $databaseTables;
            $entities[$database]['exclude'] = array();
        }
        foreach ($excludeTables as $database => $databaseTables) {
            if (array_key_exists($database, $entities)) {
                $entities[$database]['exclude'] = $databaseTables;
            }
        }

        return array($entities, $tableIncludeCount > 1);
    }

    /**
     * Normalize an array of case-insensitive strings.
     *
     * @param array<string> $values The array to normalize.
     *
     * @return array<string> The normalized array.
     */
    protected function normalizeArray(array $values)
    {
        return array_unique(
            array_map(
                function ($value) {
                    return strtolower(trim($value));
                },
                $values
            )
        );
    }

    /**
     * Normalize an array of table names.
     *
     * @param array<string> $tables          The table names.
     * @param string        $primaryDatabase The first database name in the supplied list.
     *
     * @return array<string>                       The normalized table names.
     * @throws Exception\UnqualifiedTableException If an unqualified table name was supplied.
     */
    protected function normalizeTables(array $tables, $primaryDatabase)
    {
        $normalized = array();
        foreach ($tables as $table) {
            $parts = explode('.', $table);

            if (count($parts) > 1) {
                $database = array_shift($parts);
            } else {
                if (null === $primaryDatabase) {
                    throw new Exception\UnqualifiedTableException($table);
                }

                $database = $primaryDatabase;
            }

            $normalized[$database][] = implode('.', $parts);
        }

        return $normalized;
    }

    /**
     * Write the main Dumpling header.
     *
     * @param callable $callback The callback to pass output to.
     */
    protected function writePrimaryHeader($callback)
    {
        $this->writeOutput(
            $callback,
            sprintf(
                '-- Dumpling %s' . PHP_EOL,
                DumplingApplication::VERSION
            )
        );
    }

    /**
     * Write the secondary Dumpling command header.
     *
     * @param callable $callback The callback to pass output to.
     */
    protected function writeCommandHeader($callback, array $arguments)
    {
        $displayArguments = array();
        $redactNext = false;
        foreach ($arguments as $argument) {
            if ($redactNext) {
                $redactNext = false;
            } else {
                switch ($argument) {
                    case '--user':
                    case '--host':
                    case '--port':
                        $redactNext = true;

                        break;

                    default:
                        if (0 !== strpos($argument, '--password=')) {
                            $displayArguments[] = $argument;
                        }
                }
            }
        }

        $this->writeOutput(
            $callback,
            sprintf(
                PHP_EOL .
                    '-- Dumpling executing command: %s' . PHP_EOL .
                    PHP_EOL,
                implode(' ', array_map('escapeshellarg', $displayArguments))
            )
        );
    }

    /**
     * Write arbitrary output.
     *
     * @param callable $callback The callback to pass output to.
     */
    protected function writeOutput($callback, $output)
    {
        $callback(Process::OUT, $output);
    }

    private $client;
    private $executableFinder;
    private $processFactory;
}
