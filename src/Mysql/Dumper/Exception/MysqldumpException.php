<?php

/*
 * This file is part of the Dumpling package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Dumpling\Mysql\Dumper\Exception;

use Exception;

/**
 * Represents errors thrown during mysqldump execution.
 */
class MysqldumpException extends Exception
{
    /**
     * Construct a new mysqldump exception.
     *
     * @param string         $message  The error message returned by MySQL.
     * @param integer        $exitCode The mysqldump process exit code.
     * @param Exception|null $previous The cause, if available.
     */
    public function __construct($message, $exitCode, Exception $previous = null)
    {
        parent::__construct($message, $exitCode, $previous);
    }
}
