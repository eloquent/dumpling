<?php

/*
 * This file is part of the Dumpling package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Dumpling\Mysql\Connection;

use Icecave\Isolator\Isolator;
use PHPUnit_Framework_TestCase;
use Phake;

class MysqlConnectionFactoryTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->isolator = Phake::mock(Isolator::className());
        $this->factory = new MysqlConnectionFactory($this->isolator);

        $this->connection = Phake::mock('mysqli');
        Phake::when($this->isolator)->mysqli_connect(Phake::anyParameters())->thenReturn($this->connection);
    }

    public function testCreate()
    {
        $this->assertSame($this->connection, $this->factory->create('username', 'password', 'host', 111));
        Phake::verify($this->isolator)->mysqli_connect('host', 'username', 'password', null, 111);
    }

    public function testCreateDefaults()
    {
        $this->assertSame($this->connection, $this->factory->create());
        Phake::verify($this->isolator)->mysqli_connect('localhost', 'root', null, null, 3306);
    }

    public function testCreateFailure()
    {
        Phake::when($this->isolator)->mysqli_connect(Phake::anyParameters())->thenThrow(Phake::mock('ErrorException'));

        $this->setExpectedException(__NAMESPACE__ . '\Exception\ConnectionFailedException');
        $this->factory->create('username', 'password', 'host', 111);
    }
}
