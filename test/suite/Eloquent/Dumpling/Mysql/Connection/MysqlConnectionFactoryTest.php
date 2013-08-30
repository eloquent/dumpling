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
    public function testFactory()
    {
        $isolator = Phake::mock(Isolator::className());
        $factory = new MysqlConnectionFactory($isolator);
        $connection = Phake::mock('mysqli');
        Phake::when($isolator)->mysqli_connect(Phake::anyParameters())->thenReturn($connection);

        $this->assertSame($connection, $factory->create('username', 'password', 'host', 111));
        Phake::verify($isolator)->mysqli_connect('host', 'username', 'password', null, 111);
    }
}
