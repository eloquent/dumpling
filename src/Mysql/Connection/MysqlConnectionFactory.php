<?php

/*
 * This file is part of the Dumpling package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Dumpling\Mysql\Connection;

use ErrorException;
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
     * @return mysqli                              The new MySQL connection.
     * @throws Exception\ConnectionFailedException If the connection fails.
     */
    public function create(
        $username = null,
        $password = null,
        $host = null,
        $port = null
    ) {
        if (null === $username) {
            $username = 'root';
        }
        if (null === $host) {
            $host = 'localhost';
        }
        if (null === $port) {
            $port = 3306;
        }

        try {
            $connection = $this->isolator->mysqli_connect(
                $host,
                $username,
                $password,
                null,
                $port
            );
        } catch (ErrorException $e) {
            throw new Exception\ConnectionFailedException(
                $username,
                $password,
                $host,
                $port,
                $e
            );
        }

        return $connection;
    }

    private $isolator;
}
