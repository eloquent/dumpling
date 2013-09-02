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

use Eloquent\Dumpling\Mysql\Client\MysqlClient;
use Eloquent\Dumpling\Mysql\Client\MysqlClientFactory;
use PHPUnit_Framework_TestCase;
use Phake;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class DumplingCommandTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->clientFactory = new MysqlClientFactory;
        $this->dumperFactory = Phake::mock('Eloquent\Dumpling\Mysql\Dumper\MysqlDumperFactoryInterface');
        $this->command = new DumplingCommand($this->clientFactory, $this->dumperFactory);

        $this->dumper = Phake::mock('Eloquent\Dumpling\Mysql\Dumper\MysqlDumperInterface');
        Phake::when($this->dumperFactory)->create(Phake::anyParameters())->thenReturn($this->dumper);
    }

    public function testConstructor()
    {
        $expectedDefinition = new InputDefinition;
        $expectedDefinition->addArgument(
            new InputArgument(
                'database',
                InputArgument::OPTIONAL,
                'The database to dump.'
            )
        );
        $expectedDefinition->addArgument(
            new InputArgument(
                'table',
                InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
                'The table(s) to dump.'
            )
        );

        $expectedDefinition->addOption(
            new InputOption(
                'database',
                'D',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Additional database(s) to dump.'
            )
        );
        $expectedDefinition->addOption(
            new InputOption(
                'exclude-database',
                'X',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Database(s) to ignore.'
            )
        );
        $expectedDefinition->addOption(
            new InputOption(
                'table',
                'T',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Additional table(s) to dump. Expects database.table format.'
            )
        );
        $expectedDefinition->addOption(
            new InputOption(
                'exclude-table',
                'x',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Table(s) to ignore.'
            )
        );
        $expectedDefinition->addOption(
            new InputOption(
                'no-data',
                'd',
                InputOption::VALUE_NONE,
                'Do not dump table data.'
            )
        );
        $expectedDefinition->addOption(
            new InputOption(
                'skip-locks',
                'L',
                InputOption::VALUE_NONE,
                'Do not lock tables.'
            )
        );
        $expectedDefinition->addOption(
            new InputOption(
                'transaction',
                't',
                InputOption::VALUE_NONE,
                'Dump data in a transactional manner. Only works for InnoDB tables.'
            )
        );
        $expectedDefinition->addOption(
            new InputOption(
                'host',
                'H',
                InputOption::VALUE_REQUIRED,
                'The server hostname or IP address.',
                'localhost'
            )
        );
        $expectedDefinition->addOption(
            new InputOption(
                'port',
                'P',
                InputOption::VALUE_REQUIRED,
                'The server port.',
                '3306'
            )
        );
        $expectedDefinition->addOption(
            new InputOption(
                'user',
                'u',
                InputOption::VALUE_REQUIRED,
                'The user to connect as.',
                'root'
            )
        );
        $expectedDefinition->addOption(
            new InputOption(
                'password',
                'p',
                InputOption::VALUE_REQUIRED,
                'The password for the user.'
            )
        );

        $this->assertSame($this->clientFactory, $this->command->clientFactory());
        $this->assertSame($this->dumperFactory, $this->command->dumperFactory());
        $this->assertSame('dumpling', $this->command->getName());
        $this->assertSame(
            'Dump the structure and contents of MySQL databases and tables.',
            $this->command->getDescription()
        );
        $this->assertEquals($expectedDefinition, $this->command->getDefinition());
    }

    public function testConstructorDefaults()
    {
        $this->command = new DumplingCommand;

        $this->assertEquals($this->clientFactory, $this->command->clientFactory());
        $this->assertInstanceOf(
            'Eloquent\Dumpling\Mysql\Dumper\MysqlDumperFactoryInterface',
            $this->command->dumperFactory()
        );
    }

    public function executeData()
    {
        return array(
            'All defaults' => array(
                '',
                null,
                null,
                null,
                null,
                false,
                null,
                null,
                null,
                null,
                true,
                false,
            ),

            'Shorthand args' => array(
                'database tableA tableB',
                null,
                null,
                null,
                null,
                false,
                array('database'),
                array('tableA', 'tableB'),
                null,
                null,
                true,
                false,
            ),

            'Connection details' => array(
                '--user username --password password --host host --port 111',
                'username',
                'password',
                'host',
                111,
                false,
                null,
                null,
                null,
                null,
                true,
                false,
            ),

            'Include/exclude' => array(
                '--database databaseA --table databaseA.tableA --database databaseB --table databaseB.tableB --exclude-database databaseC --exclude-database databaseD --exclude-table databaseC.tableC --exclude-table databaseD.tableD',
                null,
                null,
                null,
                null,
                false,
                array('databaseA', 'databaseB'),
                array('databaseA.tableA', 'databaseB.tableB'),
                array('databaseC', 'databaseD'),
                array('databaseC.tableC', 'databaseD.tableD'),
                true,
                false,
            ),

            'Mixed shorthand and options' => array(
                'databaseA databaseA.tableA --database databaseB --table databaseB.tableB',
                null,
                null,
                null,
                null,
                false,
                array('databaseA', 'databaseB'),
                array('databaseA.tableA', 'databaseB.tableB'),
                null,
                null,
                true,
                false,
            ),

            'Flags' => array(
                '--no-data --skip-locks --transaction',
                null,
                null,
                null,
                null,
                true,
                null,
                null,
                null,
                null,
                false,
                true,
            ),
        );
    }

    /**
     * @dataProvider executeData
     */
    public function testExecute($input, $user, $password, $host, $port, $noData, $databases, $tables, $excludeDatabases, $excludeTables, $useLocks, $useTransactions)
    {
        $input = new StringInput($input);
        $output = new NullOutput;
        $this->command->run($input, $output);

        Phake::verify($this->dumperFactory)->create(new MysqlClient($user, $password, $host, $port));
        Phake::verify($this->dumper)->dumpToConsole(
            $output,
            $noData,
            $databases,
            $tables,
            $excludeDatabases,
            $excludeTables,
            $useLocks,
            $useTransactions
        );
    }
}
