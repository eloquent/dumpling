# Dumpling

*Diff-friendly mysqldump with an improved interface.*

[![The most recent stable version is 0.2.2][version-image]][Semantic versioning]
[![Current build status image][build-image]][Current build status]
[![Current coverage status image][coverage-image]][Current coverage status]

## Installation and documentation

- Available as [Composer] package [eloquent/dumpling].
- [Executable phar] available for download.
- [API documentation] available.

## What is *Dumpling*?

*Dumpling* is a wrapper for [mysqldump] that uses sensible defaults, and
provides a streamlined interface. *Dumpling* is designed to be as intuitive and
simple to use as possible.

*Dumpling* still uses mysqldump internally, so it's 100% compatible. It also
produces output which is much more diff-friendly than standard mysqldump.

## What's wrong with mysqldump?

- Outputs internal databases like `mysql` and `information_schema` which are
  useless in 99% of cases (and potentially dangerous when importing).
- Extreme line length on extended INSERT statements causes havok for many text
  editors.
- Diffing of extended INSERT statements is useless, because of line length, and
  unpredictable row order.
- Leaves out important procedure and function information by default.
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

### Excluding databases

    dumpling --exclude-database database_a

### Excluding data

    dumpling --no-data

### Dumping one specific database

    dumpling database_name

### Dumping one specific table

    dumpling database_name table_name

### Dumping two specific tables from different databases

    dumpling --table database_a.table --table database_b.table

## Requirements

- [PHP] >= 5.3
- [mysqldump] >= 5.1.2

<!-- References -->

[Executable phar]: http://lqnt.co/dumpling/dumpling
[mysqldump]: http://dev.mysql.com/doc/refman/5.7/en/mysqldump.html
[PHP]: http://php.net/

[API documentation]: http://lqnt.co/dumpling/artifacts/documentation/api/
[Composer]: http://getcomposer.org/
[build-image]: http://img.shields.io/travis/eloquent/dumpling/develop.svg "Current build status for the develop branch"
[Current build status]: https://travis-ci.org/eloquent/dumpling
[coverage-image]: http://img.shields.io/coveralls/eloquent/dumpling/develop.svg "Current test coverage for the develop branch"
[Current coverage status]: https://coveralls.io/r/eloquent/dumpling
[eloquent/dumpling]: https://packagist.org/packages/eloquent/dumpling
[Semantic versioning]: http://semver.org/
[version-image]: http://img.shields.io/:semver-0.2.2-yellow.svg "This project uses semantic versioning"
