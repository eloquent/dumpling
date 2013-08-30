<?php

/*
 * This file is part of the Dumpling package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Dumpling\Mysql\Client;

use Eloquent\Dumpling\Mysql\Connection\MysqlConnectionFactory;
use Eloquent\Dumpling\Mysql\Connection\MysqlConnectionFactoryInterface;
use mysqli;

/**
 * Retrieves information about MySQL servers.
 */
class MysqlClient implements MysqlClientInterface
{
    /**
     * Construct a new MySQL client.
     *
     * @param string|null                          $username          The username to connect as.
     * @param string|null                          $password          The password for the user.
     * @param string|null                          $host              The hostname or IP address of the server.
     * @param integer|null                         $port              The port of the server.
     * @param MysqlConnectionFactoryInterface|null $connectionFactory The connection factory to use.
     */
    public function __construct(
        $username = null,
        $password = null,
        $host = null,
        $port = null,
        MysqlConnectionFactoryInterface $connectionFactory = null
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
        if (null === $connectionFactory) {
            $connectionFactory = new MysqlConnectionFactory;
        }

        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->connectionFactory = $connectionFactory;
    }

    /**
     * Handles closing the internal MySQL connection on desctruction.
     */
    public function __destruct()
    {
        if (null !== $this->connection) {
            $this->connection->close();
        }
    }

    /**
     * Get the username.
     *
     * @return string The username.
     */
    public function username()
    {
        return $this->username;
    }

    /**
     * Get the password.
     *
     * @return string|null The password, or null if no password was supplied.
     */
    public function password()
    {
        return $this->password;
    }

    /**
     * Get the hostname or IP address of the server.
     *
     * @return string The hostname or IP address.
     */
    public function host()
    {
        return $this->host;
    }

    /**
     * Get the port of the server.
     *
     * @return integer The port.
     */
    public function port()
    {
        return $this->port;
    }

    /**
     * Get the connection factory.
     *
     * @return MysqlConnectionFactoryInterface The connection factory.
     */
    public function connectionFactory()
    {
        return $this->connectionFactory;
    }

    /**
     * Get the list of database names.
     *
     * @return array<string> The database names.
     */
    public function listDatabases()
    {
        $result = $this->connection()->query(
            'SELECT SCHEMA_NAME FROM information_schema.SCHEMATA'
        );

        $databases = array();
        while ($row = $result->fetch_array(MYSQLI_NUM)) {
            $databases[] = $row[0];
        }

        return $databases;
    }

    /**
     * Create a new MySQL connection, or return the existing one.
     *
     * @return mysqli The MySQL connection.
     */
    protected function connection()
    {
        if (null === $this->connection) {
            $this->connection = $this->connectionFactory()->create(
                $this->username(),
                $this->password(),
                $this->host(),
                $this->port()
            );
        }

        return $this->connection;
    }

    private $username;
    private $password;
    private $host;
    private $port;
    private $connection;
    private $connectionFactory;
}
