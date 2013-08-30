<?php

/*
 * This file is part of the Dumpling package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Dumpling\Process;

use Symfony\Component\Process\ProcessBuilder;

/**
 * Creates processes.
 */
class ProcessFactory implements ProcessFactoryInterface
{
    /**
     * Create a new process.
     *
     * @param array<string> $arguments The process arguments.
     *
     * @return Process
     */
    public function create(array $arguments)
    {
        $builder = new ProcessBuilder($arguments);

        return $builder->getProcess();
    }
}
