<?php

/*
 * This file is part of the Dumpling package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Dumpling\Mysql\Client;

use PHPUnit_Framework_TestCase;
use Phake;

class MysqlClientTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->connectionFactory = Phake::mock('Eloquent\Dumpling\Mysql\Connection\MysqlConnectionFactoryInterface');
        $this->client = new MysqlClient('username', 'password', 'host', 111, $this->connectionFactory);

        $this->connection = Phake::mock('mysqli');
        $this->result = Phake::mock('mysqli_result');

        Phake::when($this->connectionFactory)->create(Phake::anyParameters())->thenReturn($this->connection);
        Phake::when($this->connection)->query(Phake::anyParameters())->thenReturn($this->result);
        Phake::when($this->result)->fetch_array(Phake::anyParameters())
            ->thenReturn(array('databaseA'))
            ->thenReturn(array('databaseB'))
            ->thenReturn(null)
            ->thenReturn(array('databaseC'))
            ->thenReturn(array('databaseD'))
            ->thenReturn(null);
    }

    public function testConstructor()
    {
        $this->assertSame('username', $this->client->username());
        $this->assertSame('password', $this->client->password());
        $this->assertSame('host', $this->client->host());
        $this->assertSame(111, $this->client->port());
        $this->assertSame($this->connectionFactory, $this->client->connectionFactory());
    }

    public function testConstructorDefaults()
    {
        $this->client = new MysqlClient;

        $this->assertSame('root', $this->client->username());
        $this->assertNull($this->client->password());
        $this->assertSame('localhost', $this->client->host());
        $this->assertSame(3306, $this->client->port());
        $this->assertInstanceOf(
            'Eloquent\Dumpling\Mysql\Connection\MysqlConnectionFactoryInterface',
            $this->client->connectionFactory()
        );
    }

    public function testDestructor()
    {
        unset($this->client);

        Phake::verify($this->connection, Phake::never())->close();
    }

    public function testDestructorCloseConnection()
    {
        $this->client->listDatabases();
        unset($this->client);

        Phake::verify($this->connection)->close();
    }

    public function testListDatabases()
    {
        $this->assertSame(array('databaseA', 'databaseB'), $this->client->listDatabases());
        $this->assertSame(array('databaseC', 'databaseD'), $this->client->listDatabases());
        $queryVerification = Phake::verify($this->connection, Phake::atLeast(1))
            ->query('SELECT SCHEMA_NAME FROM information_schema.SCHEMATA');
        $fetchVerification = Phake::verify($this->result, Phake::atLeast(1))->fetch_array(MYSQLI_NUM);
        Phake::inOrder(
            Phake::verify($this->connectionFactory)->create('username', 'password', 'host', 111),
            $queryVerification,
            $fetchVerification,
            $fetchVerification,
            $queryVerification,
            $fetchVerification,
            $fetchVerification
        );
    }
}
