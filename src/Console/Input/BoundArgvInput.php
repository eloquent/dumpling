<?php

/*
 * This file is part of the Dumpling package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Dumpling\Console\Input;

use Symfony\Component\Console\Input\ArgvInput;

/**
 * An input type with bound arguments.
 */
class BoundArgvInput extends ArgvInput
{
    /**
     * Construct a new bound argv input.
     *
     * @param array<integer,string>      $boundArguments The bound arguments.
     * @param array<integer,string>|null $argv           The argv values.
     * @param InputDefinition|null       $definition     The input definition.
     *
     * @throws Exception\UndefinedArgvException If argv information cannot be determined.
     */
    public function __construct(
        array $boundArguments,
        array $argv = null,
        InputDefinition $definition = null
    ) {
        if (null === $argv) {
            if (!array_key_exists('argv', $_SERVER)) {
                throw new Exception\UndefinedArgvException;
            }

            $argv = $_SERVER['argv'];
        }

        array_splice($argv, 1, 0, $boundArguments);

        parent::__construct($argv, $definition);
    }
}
