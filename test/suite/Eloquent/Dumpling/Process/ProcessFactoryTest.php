<?php

/*
 * This file is part of the Dumpling package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Dumpling\Process;

use PHPUnit_Framework_TestCase;
use Symfony\Component\Process\Process;

class ProcessFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $factory = new ProcessFactory;
        $actual = $factory->create(array('a', 'b', 'c'));
        $expected = new Process("'a' 'b' 'c'");

        $this->assertEquals($expected, $actual);
    }
}
