<!-- markdownlint-disable MD013 -->
# Converter Reference

* column `Console Tool` indicates when the PHP linter does not support natively reporting, if the `convert` command is required
* column `Reporting` indicates if the PHP linter support natively to extends reporting feature with a custom class

## Available since v1.0.0

| Converter                                            |          User Guide           | Console Tool | Reporting |
|:-----------------------------------------------------|:-----------------------------:|:------------:|:---------:|
| Easy-Coding-Standard [official website][ecs]         |         [ecs](ecs.md)         |   required   |     ❌     |
| PHP_CodeSniffer [official website][phpcs]            |       [phpcs](phpcs.md)       |   optional   |     ✅     |
| PHPLint [official website][phplint]                  |     [phplint](phplint.md)     |   optional   |     ✅     |
| PHP Mess Detector [official website][phpmd]          |       [phpmd](phpmd.md)       |   required   |     ❌     |
| PHPStan [official website][phpstan]                  |     [phpstan](phpstan.md)     |   optional   |     ✅     |

[ecs]: https://github.com/easy-coding-standard/easy-coding-standard
[phpcs]: https://github.com/PHPCSStandards/PHP_CodeSniffer
[phplint]: https://github.com/overtrue/phplint
[phpmd]: https://github.com/phpmd/phpmd
[phpstan]: https://github.com/phpstan/phpstan
