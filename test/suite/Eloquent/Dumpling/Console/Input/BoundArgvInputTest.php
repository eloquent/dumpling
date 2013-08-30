<?php

/*
 * This file is part of the Dumpling package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Dumpling\Console\Input;

use Eloquent\Liberator\Liberator;
use PHPUnit_Framework_TestCase;

class BoundArgvInputTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->_serverArray = $_SERVER;
    }

    protected function tearDown()
    {
        parent::tearDown();

        $_SERVER = $this->_serverArray;
    }

    public function testConstructor()
    {
        $input = new BoundArgvInput(
            array('foo', 'bar'),
            array('baz', 'qux')
        );

        $this->assertSame(
            array('foo', 'bar', 'qux'),
            Liberator::liberate($input)->tokens
        );
    }

    public function testConstructorDefaults()
    {
        $_SERVER['argv'] = array('baz', 'qux');
        $input = new BoundArgvInput(
            array('foo', 'bar')
        );

        $this->assertSame(
            array('foo', 'bar', 'qux'),
            Liberator::liberate($input)->tokens
        );
    }

    public function testConstructorDefaultsNoArgv()
    {
        unset($_SERVER['argv']);

        $this->setExpectedException(__NAMESPACE__ . '\Exception\UndefinedArgvException');
        new BoundArgvInput(array());
    }
}
