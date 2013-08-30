<?php

/*
 * This file is part of the Dumpling package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Dumpling\Console\Input\Exception;

use Exception;
use PHPUnit_Framework_TestCase;

class UndefinedArgvExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $previous = new Exception;
        $exception = new UndefinedArgvException($previous);

        $this->assertSame('$_SERVER[\'argv\'] is undefined.', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
