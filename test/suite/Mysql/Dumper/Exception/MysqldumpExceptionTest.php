<?php

/*
 * This file is part of the Dumpling package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Dumpling\Mysql\Dumper\Exception;

use Exception;
use PHPUnit_Framework_TestCase;

class MysqldumpExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $previous = new Exception;
        $exception = new MysqldumpException('mysql error', 111, $previous);

        $this->assertSame('mysql error', $exception->getMessage());
        $this->assertSame(111, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
