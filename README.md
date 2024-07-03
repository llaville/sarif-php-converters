<!-- markdownlint-disable MD013 -->
# SARIF PHP Converters

[![StandWithUkraine](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/badges/StandWithUkraine.svg)](https://github.com/vshymanskyy/StandWithUkraine/blob/main/docs/README.md)
[![GitHub Discussions](https://img.shields.io/github/discussions/llaville/umlwriter)](https://github.com/llaville/sarif-php-sdk/discussions)

| Releases      |                    Branch                     |                               PHP                               |                          Packagist                          |                     License                      |                           Documentation                            |
|:--------------|:---------------------------------------------:|:---------------------------------------------------------------:|:-----------------------------------------------------------:|:------------------------------------------------:|:------------------------------------------------------------------:|
| Stable v1.0.x | [![Branch 1.0][Branch_100x-img]][Branch_100x] | [![Minimum PHP Version)][PHPVersion_100x-img]][PHPVersion_100x] | [![Stable Version 1.0][Packagist_100x-img]][Packagist_100x] | [![License 1.0][License_100x-img]][License_100x] | [![Documentation 1.0][Documentation_100x-img]][Documentation_100x] |

[Branch_100x-img]: https://img.shields.io/badge/branch-1.0-orange
[Branch_100x]: https://github.com/llaville/sarif-php-converters/tree/1.0
[PHPVersion_100x-img]: https://img.shields.io/packagist/php-v/bartlett/sarif-php-converters/1.0.0
[PHPVersion_100x]: https://www.php.net/supported-versions.php
[Packagist_100x-img]: https://img.shields.io/badge/packagist-v1.0.0-blue
[Packagist_100x]: https://packagist.org/packages/bartlett/sarif-php-converters
[License_100x-img]: https://img.shields.io/packagist/l/bartlett/sarif-php-converters
[License_100x]: https://github.com/llaville/sarif-php-converters/blob/1.0/LICENSE
[Documentation_100x-img]: https://img.shields.io/badge/documentation-v1.0-green
[Documentation_100x]: https://github.com/llaville/sarif-php-converters/tree/1.0/docs

## Introduction

SARIF, the [Static Analysis Results Interchange Format][sarif-specs], defines a standard format for the output of static analysis tools.
It is a powerful and sophisticated format suited to the needs of a wide variety of tools.

There are a lot of PHP project that do [Static Code Analysis][sca], but few of them support the SARIF format.

You can consider to be lucky when they provide at least the `checkstyle` or `junit` format.

This library may be considered as a producer on `sarif` report format, for these projects that did not support yet natively.

## Documentation

All the documentation is available on [website](https://llaville.github.io/sarif-php-converters/1.0),
generated from the [docs](https://github.com/llaville/sarif-php-converters/tree/1.0/docs) folder.

[sca]: https://owasp.org/www-community/controls/Static_Code_Analysis
[sarif-specs]: https://docs.oasis-open.org/sarif/sarif/v2.1.0/sarif-v2.1.0.html
