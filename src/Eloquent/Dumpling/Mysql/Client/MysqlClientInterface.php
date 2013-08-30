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

/**
 * The interface implemented by MySQL clients.
 */
interface MysqlClientInterface
{
    /**
     * Get the username.
     *
     * @return string The username.
     */
    public function username();

    /**
     * Get the password.
     *
     * @return string|null The password, or null if no password was supplied.
     */
    public function password();

    /**
     * Get the hostname or IP address of the server.
     *
     * @return string The hostname or IP address.
     */
    public function host();

    /**
     * Get the port of the server.
     *
     * @return integer The port.
     */
    public function port();

    /**
     * Get the list of database names.
     *
     * @return array<string> The database names.
     */
    public function listDatabases();
}
