# Dumpling

*Diff-friendly mysqldump with an improved interface.*

[![Build Status]][Latest build]
[![Test Coverage]][Test coverage report]
[![Uses Semantic Versioning]][SemVer]

## Installation and documentation

- Available as [Composer] package [eloquent/dumpling].
- [Executable phar] available for download.
- [API documentation] available.

## What is *Dumpling*?

*Dumpling* is a wrapper for [mysqldump] that uses sensible defaults, and
provides a streamlined interface. *Dumpling* is designed to be as intuitive and
simple to use as possible.

## What's wrong with mysqldump?

- Outputs internal databases like `mysql` and `information_schema` which are
  useless (and potentially dangerous) in 99% of cases.
- Extreme line length on INSERT statements causes havok for many text editors.
- Diffing of INSERT statements is useless, because of line length, and
  unpredictable row order.
- Leaves out important routine information by default.
- Command line interface is cluttered and inflexible; inclusion/exclusion of
  databases and tables is inconsistent and limited.
- Raw binary data ruins portability & human readability.
- Command line interface uses non-standard conventions for options and their
  values.

## Dumpling usage

    Usage:
     dumpling [database] [table1] ... [tableN]

    Arguments:
     database                The database to dump.
     table                   The table(s) to dump. Expects database.table format.

    Options:
     --database (-D)         Additional database(s) to dump. (multiple values allowed)
     --exclude-database (-X) Database(s) to ignore. (multiple values allowed)
     --table (-T)            Additional table(s) to dump. Expects database.table format. (multiple values allowed)
     --exclude-table (-x)    Table(s) to ignore. (multiple values allowed)
     --no-data (-d)          Do not dump table data.
     --host (-H)             The server hostname or IP address. (default: "localhost")
     --port (-P)             The server port. (default: "3306")
     --user (-u)             The user to connect as. (default: "root")
     --password (-p)         The password for the user.
     --help (-h)             Display this help message.
     --version (-V)          Display this application version.


## Usage examples

### Dumping localhost as root with no password

    dumpling

Equivalent mysqldump command:

    mysqldump \
        --host localhost \
        --port 3306 \
        --username root \
        --routines \
        --skip-extended-insert \
        --order-by-primary \
        --hex-blob \
        --protocol TCP \
        --databases <a manually-typed list of all non-internal databases> ...

### Excluding databases

    dumpling --exclude-database database_a

Equivalent mysqldump command:

    mysqldump \
        --host localhost \
        --port 3306 \
        --username root \
        --routines \
        --skip-extended-insert \
        --order-by-primary \
        --hex-blob \
        --protocol TCP \
        --databases <a manually-typed list of the desired databases> ...

### Excluding data

    dumpling --no-data

Equivalent mysqldump command:

    mysqldump \
        --host localhost \
        --port 3306 \
        --username root \
        --no-data \
        --routines \
        --skip-extended-insert \
        --order-by-primary \
        --hex-blob \
        --protocol TCP \
        --databases <a manually-typed list of all non-internal databases> ...

### Dumping one specific database

    dumpling database_name

Equivalent mysqldump command:

    mysqldump \
        --host localhost \
        --port 3306 \
        --username root \
        --routines \
        --skip-extended-insert \
        --order-by-primary \
        --hex-blob \
        --protocol TCP \
        --databases database_name

### Dumping one specific table

    dumpling database_name table_name

Equivalent mysqldump command:

    mysqldump \
    --host localhost \
    --port 3306 \
    --username root \
    --routines \
    --skip-extended-insert \
    --order-by-primary \
    --hex-blob \
    --protocol TCP \
    --databases database_name \
    --tables table_name

### Dumping two specific tables from different databases

    dumpling --table database_a.table --table database_b.table

Equivalent mysqldump commands:

    mysqldump \
    --host localhost \
    --port 3306 \
    --username root \
    --routines \
    --skip-extended-insert \
    --order-by-primary \
    --hex-blob \
    --protocol TCP \
    --databases database_a \
    --table table
    mysqldump \
    --host localhost \
    --port 3306 \
    --username root \
    --routines \
    --skip-extended-insert \
    --order-by-primary \
    --hex-blob \
    --protocol TCP \
    --databases database_b \
    --table table

## Requirements

- [PHP] >= 5.3
- [mysqldump] >= 5.1.2

<!-- References -->

[Executable phar]: http://lqnt.co/dumpling/dumpling
[mysqldump]: http://dev.mysql.com/doc/refman/5.7/en/mysqldump.html
[PHP]: http://php.net/

[API documentation]: http://lqnt.co/dumpling/artifacts/documentation/api/
[Build Status]: https://api.travis-ci.org/eloquent/dumpling.png?branch=master
[Composer]: http://getcomposer.org/
[eloquent/dumpling]: https://packagist.org/packages/eloquent/dumpling
[Latest build]: https://travis-ci.org/eloquent/dumpling
[SemVer]: http://semver.org/
[Test coverage report]: https://coveralls.io/r/eloquent/dumpling
[Test Coverage]: https://coveralls.io/repos/eloquent/dumpling/badge.png?branch=master
[Uses Semantic Versioning]: http://b.repl.ca/v1/semver-yes-brightgreen.png
