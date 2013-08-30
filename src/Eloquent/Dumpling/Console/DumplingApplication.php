<?php

/*
 * This file is part of the Dumpling package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Dumpling\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The Dumpling command line application.
 */
class DumplingApplication extends Application
{
    /**
     * The main Dumpling version number.
     */
    const VERSION = '0.1.0';

    /**
     * Construct a new Dumpling command line application.
     */
    public function __construct()
    {
        parent::__construct('Dumpling', static::VERSION);

        $this->add(new Command\DumplingCommand);
    }

    /**
     * Run the Dumpling command line application.
     *
     * @param InputInterface|null  $input  The input interface to use.
     * @param OutputInterface|null $output The output interface to use.
     *
     * @return integer The process exit code.
     */
    public function run(
        InputInterface $input = null,
        OutputInterface $output = null
    ) {
        if (null === $input) {
            $input = new Input\BoundArgvInput(array('dumpling'));
        }

        parent::run($input, $output);
    }
}
