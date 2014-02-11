<?php

/*
 * This file is part of the Dumpling package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Dumpling\Mysql\Client;

/**
 * Creates MySQL clients.
 */
class MysqlClientFactory implements MysqlClientFactoryInterface
{
    /**
     * Create a new MySQL client.
     *
     * @param string|null  $username The user to connect as.
     * @param string|null  $password The password for the user.
     * @param string|null  $host     The hostname or IP address of the server.
     * @param integer|null $port     The port of the server.
     *
     * @return MysqlClientInterface The new MySQL client.
     */
    public function create(
        $username = null,
        $password = null,
        $host = null,
        $port = null
    ) {
        return new MysqlClient($username, $password, $host, $port);
    }
}
