<?php

/*
 * This file is part of the Dumpling package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Dumpling\Mysql\Connection\Exception;

use Exception;
use PHPUnit_Framework_TestCase;

class ConnectionFailedExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $previous = new Exception;
        $exception = new ConnectionFailedException('username', 'password', 'host', 111, $previous);

        $this->assertSame('username', $exception->username());
        $this->assertSame('password', $exception->password());
        $this->assertSame('host', $exception->host());
        $this->assertSame(111, $exception->port());
        $this->assertSame('Unable to establish a connection to MySQL.', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
