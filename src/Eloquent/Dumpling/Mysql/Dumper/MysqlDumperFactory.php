<?php

/*
 * This file is part of the Dumpling package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Dumpling\Mysql\Dumper;

use Eloquent\Dumpling\Mysql\Client\MysqlClientInterface;
use Eloquent\Dumpling\Process\ProcessFactoryInterface;
use Symfony\Component\Process\ExecutableFinder;

/**
 * The interface implemented by MySQL dumper factories.
 */
class MysqlDumperFactory implements MysqlDumperFactoryInterface
{
    /**
     * Create a new MySQL dumper.
     *
     * @param MysqlClientInterface         $client           The MySQL client to use.
     * @param ExecutableFinder|null        $executableFinder The executable finder to use.
     * @param ProcessFactoryInterface|null $processFactory   The process factory to use.
     *
     * @return MysqlDumperInterface The new MySQL dumper.
     */
    public function create(
        MysqlClientInterface $client,
        ExecutableFinder $executableFinder = null,
        ProcessFactoryInterface $processFactory = null
    ) {
        return new MysqlDumper($client, $executableFinder, $processFactory);
    }
}
