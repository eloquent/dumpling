<?php

/*
 * This file is part of the Dumpling package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Dumpling\Mysql\Dumper\Exception;

use Exception;
use PHPUnit_Framework_TestCase;

class UnqualifiedTableExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $previous = new Exception;
        $exception = new UnqualifiedTableException('table_name', $previous);

        $this->assertSame('table_name', $exception->table());
        $this->assertSame("Unqualified table name 'table_name' - use database.table form instead.", $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
