<?php

/*
 * This file is part of the Dumpling package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Dumpling\Process;

/**
 * The interface implemented by process factories.
 */
interface ProcessFactoryInterface
{
    /**
     * Create a new process.
     *
     * @param array<string> $arguments The process arguments.
     *
     * @return Process
     */
    public function create(array $arguments);
}
