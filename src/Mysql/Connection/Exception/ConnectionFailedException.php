<?php

/*
 * This file is part of the Dumpling package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Dumpling\Mysql\Connection\Exception;

use Exception;

/**
 * Unable to establish a MySQL connection.
 */
class ConnectionFailedException extends Exception
{
    /**
     * Construct a new connection failed exception.
     *
     * @param string         $username The connection username.
     * @param string|null    $password The connection password.
     * @param string         $host     The connection host or IP address.
     * @param integer        $port     The connection port.
     * @param Exception|null $previous The cause, if available.
     */
    public function __construct(
        $username,
        $password,
        $host,
        $port,
        Exception $previous = null
    ) {
        $this->username = $username;
        $this->password = $password;
        $this->host = $host;
        $this->port = $port;

        parent::__construct(
            'Unable to establish a connection to MySQL.',
            0,
            $previous
        );
    }

    /**
     * Get the connection username.
     *
     * @return string The username.
     */
    public function username()
    {
        return $this->username;
    }

    /**
     * Get the connection password.
     *
     * @return string|null The password.
     */
    public function password()
    {
        return $this->password;
    }

    /**
     * Get the connection host or IP address.
     *
     * @return string The host or IP address.
     */
    public function host()
    {
        return $this->host;
    }

    /**
     * Get the connection port.
     *
     * @return integer The port.
     */
    public function port()
    {
        return $this->port;
    }

    private $username;
    private $password;
    private $host;
    private $port;
}
