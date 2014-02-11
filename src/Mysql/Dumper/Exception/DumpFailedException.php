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
 * Dumping of MySQL information failed.
 */
class DumpFailedException extends Exception
{
    /**
     * Construct a new dump failed exception.
     *
     * @param Exception|null $previous The cause, if available.
     */
    public function __construct(Exception $previous = null)
    {
        parent::__construct('Unable to dump MySQL information.', 0, $previous);
    }
}
