<?php

/*
 * This file is part of the Dumpling package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Dumpling\Console\Command;

use Eloquent\Dumpling\Mysql\Client\MysqlClientFactory;
use Eloquent\Dumpling\Mysql\Client\MysqlClientFactoryInterface;
use Eloquent\Dumpling\Mysql\Dumper\Exception\DumpFailedException;
use Eloquent\Dumpling\Mysql\Dumper\MysqlDumperFactory;
use Eloquent\Dumpling\Mysql\Dumper\MysqlDumperFactoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Dump the structure and contents of MySQL databases and tables.
 */
class DumplingCommand extends Command
{
    /**
     * Construct a new dump command.
     *
     * @param MysqlClientFactoryInterface|null $clientFactory The MySQL client factory to use.
     * @param MysqlDumperFactoryInterface|null $dumperFactory The MySQL dumper factory to use.
     */
    public function __construct(
        MysqlClientFactoryInterface $clientFactory = null,
        MysqlDumperFactoryInterface $dumperFactory = null
    ) {
        if (null === $clientFactory) {
            $clientFactory = new MysqlClientFactory;
        }
        if (null === $dumperFactory) {
            $dumperFactory = new MysqlDumperFactory;
        }

        $this->clientFactory = $clientFactory;
        $this->dumperFactory = $dumperFactory;

        parent::__construct();
    }

    /**
     * @return MysqlClientFactoryInterface
     */
    public function clientFactory()
    {
        return $this->clientFactory;
    }

    /**
     * @return MysqlDumperFactoryInterface
     */
    public function dumperFactory()
    {
        return $this->dumperFactory;
    }

    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('dumpling');
        $this->setDescription(
            'Dump the structure and contents of MySQL databases and tables.'
        );

        $this->addArgument(
            'database',
            InputArgument::OPTIONAL,
            'The database to dump.'
        );
        $this->addArgument(
            'table',
            InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
            'The table(s) to dump. Expects database.table format.'
        );

        $this->addOption(
            'database',
            'D',
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Additional database(s) to dump.'
        );
        $this->addOption(
            'exclude-database',
            'X',
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Database(s) to ignore.'
        );
        $this->addOption(
            'table',
            'T',
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Additional table(s) to dump. Expects database.table format.'
        );
        $this->addOption(
            'exclude-table',
            'x',
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Table(s) to ignore.'
        );
        $this->addOption(
            'no-data',
            'd',
            InputOption::VALUE_NONE,
            'Do not dump table data.'
        );
        $this->addOption(
            'host',
            'H',
            InputOption::VALUE_REQUIRED,
            'The server hostname or IP address.',
            'localhost'
        );
        $this->addOption(
            'port',
            'P',
            InputOption::VALUE_REQUIRED,
            'The server port.',
            '3306'
        );
        $this->addOption(
            'user',
            'u',
            InputOption::VALUE_REQUIRED,
            'The user to connect as.',
            'root'
        );
        $this->addOption(
            'password',
            'p',
            InputOption::VALUE_REQUIRED,
            'The password for the user.'
        );
    }

    /**
     * Execute the command.
     *
     * @param InputInterface  $input  The input interface to use.
     * @param OutputInterface $output The output interface to use.
     *
     * @throws DumpFailedException If the dump failed.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dumper = $this->dumperFactory()->create(
            $this->clientFactory()->create(
                $input->getOption('user'),
                $input->getOption('password'),
                $input->getOption('host'),
                intval($input->getOption('port'))
            )
        );

        $database = $input->getArgument('database');
        $databases = $input->getOption('database');
        if (null !== $database) {
            array_unshift($databases, $database);
        }
        if (count($databases) < 1) {
            $databases = null;
        }

        $tables = array_merge(
            $input->getArgument('table'),
            $input->getOption('table')
        );
        if (count($tables) < 1) {
            $tables = null;
        }

        $excludeDatabases = $input->getOption('exclude-database');
        if (count($excludeDatabases) < 1) {
            $excludeDatabases = null;
        }

        $excludeTables = $input->getOption('exclude-table');
        if (count($excludeTables) < 1) {
            $excludeTables = null;
        }

        $dumper->dumpToConsole(
            $output,
            $input->getOption('no-data'),
            $databases,
            $tables,
            $excludeDatabases,
            $excludeTables
        );
    }

    private $clientFactory;
    private $dumperFactory;
}
