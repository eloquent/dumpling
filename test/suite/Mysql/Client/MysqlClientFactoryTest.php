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

class MysqlClientFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $factory = new MysqlClientFactory;
        $actual = $factory->create('username', 'password', 'host', 111);
        $expected = new MysqlClient('username', 'password', 'host', 111);

        $this->assertEquals($expected, $actual);
    }
}
