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
 * Unable to determine the qualified form of a supplied table name.
 */
class UnqualifiedTableException extends Exception
{
    /**
     * Construct a new unqualified table exception.
     *
     * @param string         $table    The supplied table name.
     * @param Exception|null $previous The cause, if available.
     */
    public function __construct($table, Exception $previous = null)
    {
        $this->table = $table;

        parent::__construct(
            sprintf(
                'Unqualified table name %s - use database.table form instead.',
                var_export($table, true)
            ),
            0,
            $previous
        );
    }

    /**
     * Get the table name.
     *
     * @return string The table name.
     */
    public function table()
    {
        return $this->table;
    }

    private $table;
}
