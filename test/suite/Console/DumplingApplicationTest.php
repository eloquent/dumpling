<?php

/*
 * This file is part of the Dumpling package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Dumpling\Console;

use PHPUnit_Framework_TestCase;
use Phake;

class DumplingApplicationTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->application = Phake::partialMock(__NAMESPACE__ . '\DumplingApplication');
    }

    public function testConstructor()
    {
        $this->assertSame('Dumpling', $this->application->getName());
        $this->assertSame('0.2.2', $this->application->getVersion());
        $this->assertTrue($this->application->has('dumpling'));
    }

    public function testRun()
    {
        Phake::when($this->application)->doRunCommand(Phake::anyParameters())->thenReturn(null);
        $this->application->setAutoExit(false);
        $this->application->run();
        $expectedInput = new Input\BoundArgvInput(array('dumpling'));
        $expectedInput->setInteractive(false);

        Phake::verify($this->application)->doRunCommand(
            Phake::capture($command),
            Phake::capture($input),
            Phake::capture($output)
        );
        $this->assertInstanceOf(__NAMESPACE__ . '\Command\DumplingCommand', $command);
        $this->assertEquals($expectedInput, $input);
        $this->assertInstanceOf('Symfony\Component\Console\Output\OutputInterface', $output);
    }
}
