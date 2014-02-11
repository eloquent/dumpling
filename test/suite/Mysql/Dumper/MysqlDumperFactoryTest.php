<?php

/*
 * This file is part of the Dumpling package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Dumpling\Mysql\Dumper;

use Eloquent\Dumpling\Mysql\Client\MysqlClient;
use Eloquent\Dumpling\Process\ProcessFactory;
use Symfony\Component\Process\ExecutableFinder;
use PHPUnit_Framework_TestCase;

class MysqlDumperFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $factory = new MysqlDumperFactory;
        $client = new MysqlClient('username', 'password', 'host', 111);
        $executableFinder = new ExecutableFinder;
        $processFactory = new ProcessFactory;
        $dumper = $factory->create($client, $executableFinder, $processFactory);

        $this->assertInstanceOf(__NAMESPACE__ . '\MysqlDumper', $dumper);
        $this->assertSame($client, $dumper->client());
        $this->assertSame($executableFinder, $dumper->executableFinder());
        $this->assertSame($processFactory, $dumper->processFactory());
    }
}
