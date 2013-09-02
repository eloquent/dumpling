<?php

/*
 * This file is part of the Dumpling package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Dumpling\Mysql\Dumper;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * The interface implemented by MySQL dumpers.
 */
interface MysqlDumperInterface
{
    /**
     * Dump MySQL information to a callback.
     *
     * The callback accepts two arguments. The first is the type of output,
     * which will be either Process::OUT, or Process::ERR, for standard output
     * and error output respectively. The second is the output itself.
     *
     * @param callable           $callback         The callback to pass output to.
     * @param boolean|null       $excludeData      True if data should be excluded.
     * @param array<string>|null $databases        The list of databases to dump.
     * @param array<string>|null $tables           The list of tables to dump.
     * @param array<string>|null $excludeDatabases The list of databases to exclude from the dump.
     * @param array<string>|null $excludeTables    The list of tables to exclude from the dump.
     * @param boolean|null       $useLocks         True if tables should be locked before dumping.
     * @param boolean|null       $useTransactions  True if transactions should be used when dumping.
     *
     * @throws Exception\NoDatabasesException      If there are no databases to dump.
     * @throws Exception\UnqualifiedTableException If an unqualified table name was supplied.
     * @throws Exception\DumpFailedException       If the dump failed.
     */
    public function dump(
        $callback,
        $excludeData = null,
        array $databases = null,
        array $tables = null,
        array $excludeDatabases = null,
        array $excludeTables = null,
        $useLocks = null,
        $useTransactions = null
    );

    /**
     * Dump MySQL information to a stream.
     *
     * @param stream             $output           The output stream to write to.
     * @param boolean|null       $excludeData      True if data should be excluded.
     * @param array<string>|null $databases        The list of databases to dump.
     * @param array<string>|null $tables           The list of tables to dump.
     * @param array<string>|null $excludeDatabases The list of databases to exclude from the dump.
     * @param array<string>|null $excludeTables    The list of tables to exclude from the dump.
     * @param boolean|null       $useLocks         True if tables should be locked before dumping.
     * @param boolean|null       $useTransactions  True if transactions should be used when dumping.
     *
     * @throws Exception\NoDatabasesException      If there are no databases to dump.
     * @throws Exception\UnqualifiedTableException If an unqualified table name was supplied.
     * @throws Exception\DumpFailedException       If the dump failed.
     */
    public function dumpToStream(
        $output,
        $excludeData = null,
        array $databases = null,
        array $tables = null,
        array $excludeDatabases = null,
        array $excludeTables = null,
        $useLocks = null,
        $useTransactions = null
    );

    /**
     * Dump MySQL information to a string.
     *
     * @param boolean|null       $excludeData      True if data should be excluded.
     * @param array<string>|null $databases        The list of databases to dump.
     * @param array<string>|null $tables           The list of tables to dump.
     * @param array<string>|null $excludeDatabases The list of databases to exclude from the dump.
     * @param array<string>|null $excludeTables    The list of tables to exclude from the dump.
     * @param boolean|null       $useLocks         True if tables should be locked before dumping.
     * @param boolean|null       $useTransactions  True if transactions should be used when dumping.
     *
     * @return string                              The MySQL information.
     * @throws Exception\NoDatabasesException      If there are no databases to dump.
     * @throws Exception\UnqualifiedTableException If an unqualified table name was supplied.
     * @throws Exception\DumpFailedException       If the dump failed.
     */
    public function dumpToString(
        $excludeData = null,
        array $databases = null,
        array $tables = null,
        array $excludeDatabases = null,
        array $excludeTables = null,
        $useLocks = null,
        $useTransactions = null
    );

    /**
     * Dump MySQL information to the console.
     *
     * @param OutputInterface    $output           The console output to use.
     * @param boolean|null       $excludeData      True if data should be excluded.
     * @param array<string>|null $databases        The list of databases to dump.
     * @param array<string>|null $tables           The list of tables to dump.
     * @param array<string>|null $excludeDatabases The list of databases to exclude from the dump.
     * @param array<string>|null $excludeTables    The list of tables to exclude from the dump.
     * @param boolean|null       $useLocks         True if tables should be locked before dumping.
     * @param boolean|null       $useTransactions  True if transactions should be used when dumping.
     *
     * @throws Exception\NoDatabasesException      If there are no databases to dump.
     * @throws Exception\UnqualifiedTableException If an unqualified table name was supplied.
     * @throws Exception\DumpFailedException       If the dump failed.
     */
    public function dumpToConsole(
        OutputInterface $output,
        $excludeData = null,
        array $databases = null,
        array $tables = null,
        array $excludeDatabases = null,
        array $excludeTables = null,
        $useLocks = null,
        $useTransactions = null
    );
}
