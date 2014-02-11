<?php

/*
 * This file is part of the Dumpling package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Dumpling\Console\Input\Exception;

use Exception;

/**
 * No argv values could be determined from the environment.
 */
final class UndefinedArgvException extends Exception
{
    /**
     * Construct a new undefined argv exception.
     *
     * @param Exception|null $previous The cause, if available.
     */
    public function __construct(Exception $previous = null)
    {
        parent::__construct('$_SERVER[\'argv\'] is undefined.', 0, $previous);
    }
}
