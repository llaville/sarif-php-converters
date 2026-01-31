<!-- markdownlint-disable MD013 MD033 -->
# SARIF PHP Converters

[![StandWithUkraine](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/badges/StandWithUkraine.svg)](https://github.com/vshymanskyy/StandWithUkraine/blob/main/docs/README.md)
[![GitHub Discussions](https://img.shields.io/github/discussions/llaville/sarif-php-converters)](https://github.com/llaville/sarif-php-converters/discussions)

| Releases      |                    Branch                     |                               PHP                               |                          Packagist                          |                     License                      |                           Documentation                            |
|:--------------|:---------------------------------------------:|:---------------------------------------------------------------:|:-----------------------------------------------------------:|:------------------------------------------------:|:------------------------------------------------------------------:|
| Stable v1.0.x | [![Branch 1.0][Branch_100x-img]][Branch_100x] | [![Minimum PHP Version)][PHPVersion_100x-img]][PHPVersion_100x] | [![Stable Version 1.0][Packagist_100x-img]][Packagist_100x] | [![License 1.0][License_100x-img]][License_100x] | [![Documentation 1.0][Documentation_100x-img]][Documentation_100x] |
| Stable v1.1.x | [![Branch 1.1][Branch_101x-img]][Branch_101x] | [![Minimum PHP Version)][PHPVersion_101x-img]][PHPVersion_101x] | [![Stable Version 1.1][Packagist_101x-img]][Packagist_101x] | [![License 1.1][License_101x-img]][License_101x] | [![Documentation 1.1][Documentation_101x-img]][Documentation_101x] |
| Stable v1.2.x | [![Branch 1.2][Branch_102x-img]][Branch_102x] | [![Minimum PHP Version)][PHPVersion_102x-img]][PHPVersion_102x] | [![Stable Version 1.2][Packagist_102x-img]][Packagist_102x] | [![License 1.2][License_102x-img]][License_102x] | [![Documentation 1.2][Documentation_102x-img]][Documentation_102x] |
| Stable v1.3.x | [![Branch 1.3][Branch_103x-img]][Branch_103x] | [![Minimum PHP Version)][PHPVersion_103x-img]][PHPVersion_103x] | [![Stable Version 1.3][Packagist_103x-img]][Packagist_103x] | [![License 1.3][License_103x-img]][License_103x] | [![Documentation 1.3][Documentation_103x-img]][Documentation_103x] |
| Stable v1.4.x | [![Branch 1.4][Branch_104x-img]][Branch_104x] | [![Minimum PHP Version)][PHPVersion_104x-img]][PHPVersion_104x] | [![Stable Version 1.4][Packagist_104x-img]][Packagist_104x] | [![License 1.4][License_104x-img]][License_104x] | [![Documentation 1.4][Documentation_104x-img]][Documentation_104x] |
| Stable v1.5.x | [![Branch 1.5][Branch_105x-img]][Branch_105x] | [![Minimum PHP Version)][PHPVersion_105x-img]][PHPVersion_105x] | [![Stable Version 1.5][Packagist_105x-img]][Packagist_105x] | [![License 1.5][License_105x-img]][License_105x] | [![Documentation 1.5][Documentation_105x-img]][Documentation_105x] |

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

[Branch_101x-img]: https://img.shields.io/badge/branch-1.1-orange
[Branch_101x]: https://github.com/llaville/sarif-php-converters/tree/1.1
[PHPVersion_101x-img]: https://img.shields.io/packagist/php-v/bartlett/sarif-php-converters/1.1.2
[PHPVersion_101x]: https://www.php.net/supported-versions.php
[Packagist_101x-img]: https://img.shields.io/badge/packagist-v1.1.2-blue
[Packagist_101x]: https://packagist.org/packages/bartlett/sarif-php-converters
[License_101x-img]: https://img.shields.io/packagist/l/bartlett/sarif-php-converters
[License_101x]: https://github.com/llaville/sarif-php-converters/blob/1.1/LICENSE
[Documentation_101x-img]: https://img.shields.io/badge/documentation-v1.1-green
[Documentation_101x]: https://github.com/llaville/sarif-php-converters/tree/1.1/docs

[Branch_102x-img]: https://img.shields.io/badge/branch-1.2-orange
[Branch_102x]: https://github.com/llaville/sarif-php-converters/tree/1.2
[PHPVersion_102x-img]: https://img.shields.io/packagist/php-v/bartlett/sarif-php-converters/1.2.0
[PHPVersion_102x]: https://www.php.net/supported-versions.php
[Packagist_102x-img]: https://img.shields.io/badge/packagist-v1.2.0-blue
[Packagist_102x]: https://packagist.org/packages/bartlett/sarif-php-converters
[License_102x-img]: https://img.shields.io/packagist/l/bartlett/sarif-php-converters
[License_102x]: https://github.com/llaville/sarif-php-converters/blob/1.2/LICENSE
[Documentation_102x-img]: https://img.shields.io/badge/documentation-v1.2-green
[Documentation_102x]: https://github.com/llaville/sarif-php-converters/tree/1.2/docs

[Branch_103x-img]: https://img.shields.io/badge/branch-1.3-orange
[Branch_103x]: https://github.com/llaville/sarif-php-converters/tree/1.3
[PHPVersion_103x-img]: https://img.shields.io/packagist/php-v/bartlett/sarif-php-converters/1.3.1
[PHPVersion_103x]: https://www.php.net/supported-versions.php
[Packagist_103x-img]: https://img.shields.io/badge/packagist-v1.3.1-blue
[Packagist_103x]: https://packagist.org/packages/bartlett/sarif-php-converters
[License_103x-img]: https://img.shields.io/packagist/l/bartlett/sarif-php-converters
[License_103x]: https://github.com/llaville/sarif-php-converters/blob/1.3/LICENSE
[Documentation_103x-img]: https://img.shields.io/badge/documentation-v1.3-green
[Documentation_103x]: https://github.com/llaville/sarif-php-converters/tree/1.3/docs

[Branch_104x-img]: https://img.shields.io/badge/branch-1.4-orange
[Branch_104x]: https://github.com/llaville/sarif-php-converters/tree/1.4
[PHPVersion_104x-img]: https://img.shields.io/packagist/php-v/bartlett/sarif-php-converters/1.4.0
[PHPVersion_104x]: https://www.php.net/supported-versions.php
[Packagist_104x-img]: https://img.shields.io/badge/packagist-v1.4.0-blue
[Packagist_104x]: https://packagist.org/packages/bartlett/sarif-php-converters
[License_104x-img]: https://img.shields.io/packagist/l/bartlett/sarif-php-converters
[License_104x]: https://github.com/llaville/sarif-php-converters/blob/1.4/LICENSE
[Documentation_104x-img]: https://img.shields.io/badge/documentation-v1.4-green
[Documentation_104x]: https://github.com/llaville/sarif-php-converters/tree/1.4/docs

[Branch_105x-img]: https://img.shields.io/badge/branch-1.5-orange
[Branch_105x]: https://github.com/llaville/sarif-php-converters/tree/1.5
[PHPVersion_105x-img]: https://img.shields.io/packagist/php-v/bartlett/sarif-php-converters/1.5.0
[PHPVersion_105x]: https://www.php.net/supported-versions.php
[Packagist_105x-img]: https://img.shields.io/badge/packagist-v1.5.0-blue
[Packagist_105x]: https://packagist.org/packages/bartlett/sarif-php-converters
[License_105x-img]: https://img.shields.io/packagist/l/bartlett/sarif-php-converters
[License_105x]: https://github.com/llaville/sarif-php-converters/blob/1.5/LICENSE
[Documentation_105x-img]: https://img.shields.io/badge/documentation-v1.5-green
[Documentation_105x]: https://github.com/llaville/sarif-php-converters/tree/1.5/docs

## Introduction

SARIF, the [Static Analysis Results Interchange Format][sarif-specs], defines a standard format for the output of static analysis tools.
It is a powerful and sophisticated format suited to the needs of a wide variety of tools.

There are a lot of PHP project that do [Static Code Analysis][sca], but few of them support the SARIF format.

You can consider to be lucky when they provide at least the `checkstyle` or `junit` format.

This library may be considered as a producer on `sarif` report format, for these projects that did not support yet natively.

## Features at a glance

* A Console Tool to allow conversion from `checkstyle` format to `sarif`, if your application does not support custom reporting class.
* Add `sarif` output format support to your application, if you accept to load on fly, any custom reporting class.

## Comparison by project on output formats

[cda]: https://github.com/shipmonk-rnd/composer-dependency-analyser
[composer]: https://github.com/composer/composer
[ecs]: https://github.com/easy-coding-standard/easy-coding-standard
[phan]: https://github.com/phan/phan
[phpcs]: https://github.com/PHPCSStandards/PHP_CodeSniffer
[phpcs-fixer]: https://github.com/PHP-CS-Fixer/PHP-CS-Fixer
[phpinsights]: https://github.com/nunomaduro/phpinsights
[phplint]: https://github.com/overtrue/phplint
[phpmd]: https://github.com/phpmd/phpmd
[phpstan]: https://github.com/phpstan/phpstan
[rector]: https://github.com/rectorphp/rector
[twigcs-fixer]: https://github.com/VincentLanglet/Twig-CS-Fixer

| Project                             | Version | SARIF | Checkstyle | JUnit | CodeClimate<br/>GitLab | GitHub |                     Output Format Support                      |
|:------------------------------------|:-------:|:-----:|:----------:|:-----:|:----------------------:|:------:|:--------------------------------------------------------------:|
| [Composer Dependency Analyser][cda] |  1.6.x  |   ❌   |     ❌      |   ✅   |           ❌            |   ❌    |                             junit                              |
| [Composer][composer]                |  2.4.x  |   ❌   |     ❌      |   ❌   |           ❌            |   ❌    |                    table,plain,json,summary                    |
| [Easy-Coding-Standard][ecs]         | 12.3.x  |   ❌   |     ✅      |   ✅   |           ✅            |   ❌    |              console,json,junit,checkstyle,gitlab              |
| [Phan][phan]                        |  5.5.1  |   ❌   |     ✅      |   ❌   |           ✅            |   ✅    |    text,csv,json,checkstyle,codeclimate,pylint,html,github     |
| [PHP CodeSniffer][phpcs]            | 3.10.x  |   ❌   |     ✅      |   ✅   |           ❌            |   ❌    |                     json,checkstyle,junit                      |
| [PHP-CS-Fixer][phpcs-fixer]         | 3.59.x  |   ❌   |     ✅      |   ✅   |           ✅            |   ❌    |                  checkstyle,gitlab,json,junit                  |
| [PHPInsights][phpinsights]          | 2.11.x  |   ❌   |     ✅      |   ❌   |           ✅            |   ✅    |       console,json,checkstyle,codeclimate,github-action        |
| [PHPLint][phplint]                  |  9.4.x  |  (✅)  |     ✅      |   ✅   |           ❌            |   ❌    |             console,json,junit,checkstyle,(sarif)              |
| [PHP Mess Detector][phpmd]          | 2.15.x  |   ✅   |     ✅      |   ❌   |           ✅            |   ✅    |       xml,text,html,json,github,gitlab,sarif,checkstyle        |
| [PHPStan][phpstan]                  |  2.0.x  |  (✅)  |     ✅      |   ✅   |           ✅            |   ✅    | table,raw,checkstyle,json,junit,github,gitlab,teamcity,(sarif) |
| [Rector][rector]                    |  2.0.x  |   ❌   |     ❌      |   ✅   |           ✅            |   ✅    |                console,json,junit,gitlab,github                |
| [Twig-CS-Fixer][twigcs-fixer]       |  3.0.x  |   ❌   |     ✅      |   ✅   |           ❌            |   ✅    |               null,text,checkstyle,junit,github                |

Legend :

* ✅ indicate that support to the format is available
* (✅) indicate that support to the format is optional
* ❌ indicate that support to the format is not yet available

## Documentation

All the documentation is available on [website](https://llaville.github.io/sarif-php-converters/1.5),
generated from the [docs](https://github.com/llaville/sarif-php-converters/tree/1.5/docs) folder.

[sca]: https://owasp.org/www-community/controls/Static_Code_Analysis
[sarif-specs]: https://docs.oasis-open.org/sarif/sarif/v2.1.0/sarif-v2.1.0.html
