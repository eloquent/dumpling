<?php

/*
 * This file is part of the Dumpling package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Dumpling\Mysql\Connection;

use Icecave\Isolator\Isolator;

/**
 * Creates MySQL connections.
 */
class MysqlConnectionFactory implements MysqlConnectionFactoryInterface
{
    /**
     * Construct a new MySQL connection factory.
     *
     * @param Isolator|null $isolator The isolator to use.
     */
    public function __construct(Isolator $isolator = null)
    {
        $this->isolator = Isolator::get($isolator);
    }

    /**
     * Create a new MySQL connection.
     *
     * @param string|null  $username The user to connect as.
     * @param string|null  $password The password for the user.
     * @param string|null  $host     The hostname or IP address of the server.
     * @param integer|null $port     The port of the server.
     *
     * @return mysqli The new MySQL connection.
     */
    public function create(
        $username = null,
        $password = null,
        $host = null,
        $port = null
    ) {
        return $this->isolator->mysqli_connect(
            $host,
            $username,
            $password,
            null,
            $port
        );
    }

    private $isolator;
}
