<?php

/*
 * This file is part of the Dumpling package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Dumpling\Mysql\Dumper;

use Eloquent\Dumpling\Console\DumplingApplication;
use PHPUnit_Framework_TestCase;
use Phake;
use Symfony\Component\Process\Process;

class MysqlDumperTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->client = Phake::partialMock(
            'Eloquent\Dumpling\Mysql\Client\MysqlClient',
            'username',
            'password',
            'host',
            111
        );
        $this->executableFinder = Phake::mock('Symfony\Component\Process\ExecutableFinder');
        $this->processFactory = Phake::mock('Eloquent\Dumpling\Process\ProcessFactoryInterface');
        $this->dumper = new MysqlDumper($this->client, $this->executableFinder, $this->processFactory);

        $this->process = Phake::mock('Symfony\Component\Process\Process');

        Phake::when($this->client)->listDatabases()->thenReturn(
            array(
                'databaseA',
                'databaseB',
                'databaseC',
                'mysql',
                'information_schema',
                'performance_schema',
            )
        );
        Phake::when($this->executableFinder)->find('mysqldump', 'mysqldump')->thenReturn('/path/to/mysqldump');
        Phake::when($this->processFactory)->create(Phake::anyParameters())->thenReturn($this->process);
        Phake::when($this->process)->run(Phake::anyParameters())->thenGetReturnByLambda(
            function ($callback) {
                $callback(Process::OUT, 'out-a' . PHP_EOL);
                $callback(Process::ERR, 'err-a' . PHP_EOL);
                $callback(Process::OUT, 'out-b' . PHP_EOL);
                $callback(Process::ERR, 'err-b' . PHP_EOL);

                return 0;
            }
        );
        Phake::when($this->process)->isSuccessful()->thenReturn(true);
    }

    public function testConstructor()
    {
        $this->assertSame($this->client, $this->dumper->client());
        $this->assertSame($this->executableFinder, $this->dumper->executableFinder());
        $this->assertSame($this->processFactory, $this->dumper->processFactory());
    }

    public function testConstructorDefaults()
    {
        $this->dumper = new MysqlDumper($this->client);

        $this->assertInstanceOf('Symfony\Component\Process\ExecutableFinder', $this->dumper->executableFinder());
        $this->assertInstanceOf('Eloquent\Dumpling\Process\ProcessFactoryInterface', $this->dumper->processFactory());
    }

    public function dumpData()
    {
        return array(
            'All defaults' => array(
                null, null, null, null, null, null, null,
                array(
                    array('/path/to/mysqldump', '--routines', '--skip-extended-insert', '--order-by-primary', '--hex-blob', '--host', 'host', '--port', '111', '--user', 'username', '--protocol', 'TCP', '--password=password', '--databases', 'databasea', 'databaseb', 'databasec'),
                ),
                array(
                    "'/path/to/mysqldump' '--routines' '--skip-extended-insert' '--order-by-primary' '--hex-blob' '--protocol' 'TCP' '--databases' 'databasea' 'databaseb' 'databasec'",
                ),
            ),

            'Flags' => array(
                true, null, null, null, null, false, true,
                array(
                    array('/path/to/mysqldump', '--routines', '--skip-extended-insert', '--order-by-primary', '--hex-blob', '--host', 'host', '--port', '111', '--user', 'username', '--protocol', 'TCP', '--password=password', '--no-data', '--skip-lock-tables', '--single-transaction', '--databases', 'databasea', 'databaseb', 'databasec'),
                ),
                array(
                    "'/path/to/mysqldump' '--routines' '--skip-extended-insert' '--order-by-primary' '--hex-blob' '--protocol' 'TCP' '--no-data' '--skip-lock-tables' '--single-transaction' '--databases' 'databasea' 'databaseb' 'databasec'"
                ),
            ),

            'Specific databases' => array(
                null, array('databaseD', 'databaseE'), null, null, null, null, null,
                array(
                    array('/path/to/mysqldump', '--routines', '--skip-extended-insert', '--order-by-primary', '--hex-blob', '--host', 'host', '--port', '111', '--user', 'username', '--protocol', 'TCP', '--password=password', '--databases', 'databased', 'databasee'),
                ),
                array(
                    "'/path/to/mysqldump' '--routines' '--skip-extended-insert' '--order-by-primary' '--hex-blob' '--protocol' 'TCP' '--databases' 'databased' 'databasee'",
                ),
            ),

            'Specific databases normally excluded' => array(
                null, array('mysql', 'information_schema', 'performance_schema'), null, null, null, null, null,
                array(
                    array('/path/to/mysqldump', '--routines', '--skip-extended-insert', '--order-by-primary', '--hex-blob', '--host', 'host', '--port', '111', '--user', 'username', '--protocol', 'TCP', '--password=password', '--databases', 'mysql', 'information_schema', 'performance_schema'),
                ),
                array(
                    "'/path/to/mysqldump' '--routines' '--skip-extended-insert' '--order-by-primary' '--hex-blob' '--protocol' 'TCP' '--databases' 'mysql' 'information_schema' 'performance_schema'",
                ),
            ),

            'Specific tables' => array(
                null, null, array('database.tableA', 'database.tableB'), null, null, null, null,
                array(
                    array('/path/to/mysqldump', '--routines', '--skip-extended-insert', '--order-by-primary', '--hex-blob', '--host', 'host', '--port', '111', '--user', 'username', '--protocol', 'TCP', '--password=password', '--databases', 'database', '--tables', 'tablea', 'tableb'),
                ),
                array(
                    "'/path/to/mysqldump' '--routines' '--skip-extended-insert' '--order-by-primary' '--hex-blob' '--protocol' 'TCP' '--databases' 'database' '--tables' 'tablea' 'tableb'",
                ),
            ),

            'Specific tables unqualified' => array(
                null, array('database'), array('tableA', 'tableB'), null, null, null, null,
                array(
                    array('/path/to/mysqldump', '--routines', '--skip-extended-insert', '--order-by-primary', '--hex-blob', '--host', 'host', '--port', '111', '--user', 'username', '--protocol', 'TCP', '--password=password', '--databases', 'database', '--tables', 'tablea', 'tableb'),
                ),
                array(
                    "'/path/to/mysqldump' '--routines' '--skip-extended-insert' '--order-by-primary' '--hex-blob' '--protocol' 'TCP' '--databases' 'database' '--tables' 'tablea' 'tableb'",
                ),
            ),

            'Specific tables across databases' => array(
                null, null, array('databaseA.tableA', 'databaseB.tableB'), null, null, null, null,
                array(
                    array('/path/to/mysqldump', '--routines', '--skip-extended-insert', '--order-by-primary', '--hex-blob', '--host', 'host', '--port', '111', '--user', 'username', '--protocol', 'TCP', '--password=password', '--databases', 'databasea', '--tables', 'tablea'),
                    array('/path/to/mysqldump', '--routines', '--skip-extended-insert', '--order-by-primary', '--hex-blob', '--host', 'host', '--port', '111', '--user', 'username', '--protocol', 'TCP', '--password=password', '--databases', 'databaseb', '--tables', 'tableb'),
                ),
                array(
                    "'/path/to/mysqldump' '--routines' '--skip-extended-insert' '--order-by-primary' '--hex-blob' '--protocol' 'TCP' '--databases' 'databasea' '--tables' 'tablea'",
                    "'/path/to/mysqldump' '--routines' '--skip-extended-insert' '--order-by-primary' '--hex-blob' '--protocol' 'TCP' '--databases' 'databaseb' '--tables' 'tableb'",
                ),
            ),

            'Exclude databases' => array(
                null, null, null, array('databasea', 'databasec'), null, null, null,
                array(
                    array('/path/to/mysqldump', '--routines', '--skip-extended-insert', '--order-by-primary', '--hex-blob', '--host', 'host', '--port', '111', '--user', 'username', '--protocol', 'TCP', '--password=password', '--databases', 'databaseb'),
                ),
                array(
                    "'/path/to/mysqldump' '--routines' '--skip-extended-insert' '--order-by-primary' '--hex-blob' '--protocol' 'TCP' '--databases' 'databaseb'",
                ),
            ),

            'Exclude tables' => array(
                null, null, null, null, array('databasea.tablea', 'databaseb.tableb'), null, null,
                array(
                    array('/path/to/mysqldump', '--routines', '--skip-extended-insert', '--order-by-primary', '--hex-blob', '--host', 'host', '--port', '111', '--user', 'username', '--protocol', 'TCP', '--password=password', '--databases', 'databasea', 'databaseb', 'databasec', '--ignore-table', 'databasea.tablea', '--ignore-table', 'databaseb.tableb'),
                ),
                array(
                    "'/path/to/mysqldump' '--routines' '--skip-extended-insert' '--order-by-primary' '--hex-blob' '--protocol' 'TCP' '--databases' 'databasea' 'databaseb' 'databasec' '--ignore-table' 'databasea.tablea' '--ignore-table' 'databaseb.tableb'",
                ),
            ),

            'All options' => array(
                true, array('databaseD', 'databaseE'), array('databaseA.tableA', 'databaseB.tableB'), array('databased', 'databasee'), array('databasea.tablec', 'databaseb.tabled', 'databasef.tablef', 'databaseg.tableg'), false, true,
                array(
                    array('/path/to/mysqldump', '--routines', '--skip-extended-insert', '--order-by-primary', '--hex-blob', '--host', 'host', '--port', '111', '--user', 'username', '--protocol', 'TCP', '--password=password', '--no-data',  '--skip-lock-tables', '--single-transaction', '--databases', 'databased'),
                    array('/path/to/mysqldump', '--routines', '--skip-extended-insert', '--order-by-primary', '--hex-blob', '--host', 'host', '--port', '111', '--user', 'username', '--protocol', 'TCP', '--password=password', '--no-data',  '--skip-lock-tables', '--single-transaction', '--databases', 'databasee'),
                    array('/path/to/mysqldump', '--routines', '--skip-extended-insert', '--order-by-primary', '--hex-blob', '--host', 'host', '--port', '111', '--user', 'username', '--protocol', 'TCP', '--password=password', '--no-data',  '--skip-lock-tables', '--single-transaction', '--databases', 'databasea', '--tables', 'tablea', '--ignore-table', 'databasea.tablec'),
                    array('/path/to/mysqldump', '--routines', '--skip-extended-insert', '--order-by-primary', '--hex-blob', '--host', 'host', '--port', '111', '--user', 'username', '--protocol', 'TCP', '--password=password', '--no-data',  '--skip-lock-tables', '--single-transaction', '--databases', 'databaseb', '--tables', 'tableb', '--ignore-table', 'databaseb.tabled'),
                ),
                array(
                    "'/path/to/mysqldump' '--routines' '--skip-extended-insert' '--order-by-primary' '--hex-blob' '--protocol' 'TCP' '--no-data' '--skip-lock-tables' '--single-transaction' '--databases' 'databased'",
                    "'/path/to/mysqldump' '--routines' '--skip-extended-insert' '--order-by-primary' '--hex-blob' '--protocol' 'TCP' '--no-data' '--skip-lock-tables' '--single-transaction' '--databases' 'databasee'",
                    "'/path/to/mysqldump' '--routines' '--skip-extended-insert' '--order-by-primary' '--hex-blob' '--protocol' 'TCP' '--no-data' '--skip-lock-tables' '--single-transaction' '--databases' 'databasea' '--tables' 'tablea' '--ignore-table' 'databasea.tablec'",
                    "'/path/to/mysqldump' '--routines' '--skip-extended-insert' '--order-by-primary' '--hex-blob' '--protocol' 'TCP' '--no-data' '--skip-lock-tables' '--single-transaction' '--databases' 'databaseb' '--tables' 'tableb' '--ignore-table' 'databaseb.tabled'",
                ),
            ),
        );
    }

    /**
     * @dataProvider dumpData
     */
    public function testDump(
        $excludeData,
        $databases,
        $tables,
        $excludeDatabases,
        $excludeTables,
        $useLocks,
        $useTransactions,
        $commandArgumentSets,
        $displayCommands
    ) {
        $output = '';
        $error = '';
        $callback = function ($type, $buffer) use (&$output, &$error) {
            if (Process::ERR === $type) {
                $error .= $buffer;
            } else {
                $output .= $buffer;
            }
        };
        $this->dumper->dump(
            $callback,
            $excludeData,
            $databases,
            $tables,
            $excludeDatabases,
            $excludeTables,
            $useLocks,
            $useTransactions
        );
        $expectedOutput = array(
            '-- Dumpling ' . DumplingApplication::VERSION,
            '',
        );
        $expectedError = array();
        foreach ($displayCommands as $displayCommand) {
            $expectedOutput[] = sprintf('-- Dumpling executing command: %s', $displayCommand);
            $expectedOutput[] = '';
            $expectedOutput[] = 'out-a';
            $expectedOutput[] = 'out-b';
            $expectedOutput[] = '';

            $expectedError[] = 'err-a';
            $expectedError[] = 'err-b';
        }
        $expectedError[] = '';

        $this->assertSame(implode(PHP_EOL, $expectedOutput), $output);
        $this->assertSame(implode(PHP_EOL, $expectedError), $error);
        Phake::verify($this->processFactory, Phake::times(count($commandArgumentSets)))->create(Phake::anyParameters());
        $verifications = array();
        foreach ($commandArgumentSets as $commandArguments) {
            $verifications[] = Phake::verify($this->processFactory)->create($commandArguments);
        }
        call_user_func_array('Phake::inOrder', $verifications);
    }

    public function testDumpFailureNoDatabases()
    {
        Phake::when($this->client)->listDatabases()->thenReturn(array());

        $this->setExpectedException(__NAMESPACE__ . '\Exception\NoDatabasesException');
        $this->dumper->dump(function() {});
    }

    public function testDumpFailureUnqualifiedTable()
    {
        Phake::when($this->client)->listDatabases()->thenReturn(array());

        $this->setExpectedException(__NAMESPACE__ . '\Exception\UnqualifiedTableException');
        $this->dumper->dump(function() {}, null, array(), array('table'));
    }

    public function testDumpFailureMysqldumpError()
    {
        Phake::when($this->process)->run(Phake::anyParameters())->thenReturn(222);
        Phake::when($this->process)->isSuccessful()->thenReturn(false);
        Phake::when($this->process)->getErrorOutput()->thenReturn('mysqldump error');

        try {
            $this->dumper->dump(function() {});
            $this->fail();
        } catch (Exception\DumpFailedException $e) {
            $this->assertInstanceOf(__NAMESPACE__ . '\Exception\MysqldumpException', $e->getPrevious());
            $this->assertSame('mysqldump error', $e->getPrevious()->getMessage());
            $this->assertSame(222, $e->getPrevious()->getCode());
        }
    }

    public function testDumpToStream()
    {
        $stream = fopen('php://memory', 'wb+');
        $this->dumper->dumpToStream($stream);
        rewind($stream);
        $output = stream_get_contents($stream);
        fclose($stream);
        $expectedOutput = array(
            '-- Dumpling ' . DumplingApplication::VERSION,
            '',
            "-- Dumpling executing command: '/path/to/mysqldump' '--routines' '--skip-extended-insert' '--order-by-primary' '--hex-blob' '--protocol' 'TCP' '--databases' 'databasea' 'databaseb' 'databasec'",
            '',
            'out-a',
            'out-b',
            '',
        );

        $this->assertSame(implode(PHP_EOL, $expectedOutput), $output);
    }

    public function testDumpToString()
    {
        $output = $this->dumper->dumpToString();
        $expectedOutput = array(
            '-- Dumpling ' . DumplingApplication::VERSION,
            '',
            "-- Dumpling executing command: '/path/to/mysqldump' '--routines' '--skip-extended-insert' '--order-by-primary' '--hex-blob' '--protocol' 'TCP' '--databases' 'databasea' 'databaseb' 'databasec'",
            '',
            'out-a',
            'out-b',
            '',
        );

        $this->assertSame(implode(PHP_EOL, $expectedOutput), $output);
    }

    public function testDumpToConsole()
    {
        $output = '';
        $outputInterface = Phake::mock('Symfony\Component\Console\Output\OutputInterface');
        Phake::when($outputInterface)->write(Phake::anyParameters())->thenGetReturnByLambda(
            function ($data) use (&$output) {
                $output .= $data;
            }
        );
        $this->dumper->dumpToConsole($outputInterface);

        $expectedOutput = array(
            '-- Dumpling ' . DumplingApplication::VERSION,
            '',
            "-- Dumpling executing command: '/path/to/mysqldump' '--routines' '--skip-extended-insert' '--order-by-primary' '--hex-blob' '--protocol' 'TCP' '--databases' 'databasea' 'databaseb' 'databasec'",
            '',
            'out-a',
            'out-b',
            '',
        );

        $this->assertSame(implode(PHP_EOL, $expectedOutput), $output);
    }
}
