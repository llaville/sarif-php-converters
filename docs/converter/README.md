<!-- markdownlint-disable MD013 -->
# Converter Reference

* column `Console Tool` indicates when the PHP linter does not support natively reporting, if the `convert` command is required
* column `Reporting` indicates if the PHP linter support natively to extends reporting feature with a custom class

## Available since v1.0.0

| Converter                                      |           User Guide            | Console Tool | Reporting |
|:-----------------------------------------------|:-------------------------------:|:------------:|:---------:|
| Easy-Coding-Standard [official website][ecs]   |          [ecs](ecs.md)          |   required   |     ❌     |
| Phan [official website][phan]                  |         [phan](phan.md)         |   required   |     ❌     |
| PHP_CodeSniffer [official website][phpcs]      |        [phpcs](phpcs.md)        |   optional   |     ✅     |
| PHP-CS-Fixer [official website][phpcs-fixer]   |  [phpcs-fixer](phpcs-fixer.md)  |   required   |     ❌     |
| PHPLint [official website][phplint]            |      [phplint](phplint.md)      |   optional   |     ✅     |
| PHP Mess Detector [official website][phpmd]    |        [phpmd](phpmd.md)        |   required   |     ❌     |
| PHPStan [official website][phpstan]            |      [phpstan](phpstan.md)      |   optional   |     ✅     |
| Psalm [official website][psalm]                |        [psalm](psalm.md)        |   required   |     ❌     |
| Rector [official website][rector]              |       [rector](rector.md)       |   optional   |     ✅     |
| Twig-CS-Fixer [official website][twigcs-fixer] | [twigcs-fixer](twigcs-fixer.md) |   optional   |     ✅     |

[ecs]: https://github.com/easy-coding-standard/easy-coding-standard
[phan]: https://github.com/phan/phan
[phpcs]: https://github.com/PHPCSStandards/PHP_CodeSniffer
[phpcs-fixer]: https://github.com/PHP-CS-Fixer/PHP-CS-Fixer
[phplint]: https://github.com/overtrue/phplint
[phpmd]: https://github.com/phpmd/phpmd
[phpstan]: https://github.com/phpstan/phpstan
[psalm]: https://github.com/vimeo/psalm
[rector]: https://github.com/rectorphp/rector
[twigcs-fixer]: https://github.com/VincentLanglet/Twig-CS-Fixer
