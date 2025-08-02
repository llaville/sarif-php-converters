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
| Rector [official website][rector]              |       [rector](rector.md)       |   optional   |     ✅     |
| Twig-CS-Fixer [official website][twigcs-fixer] | [twigcs-fixer](twigcs-fixer.md) |   optional   |     ✅     |

## Available since v1.2.0

| Converter                             |       User Guide        | Console Tool | Reporting |
|:--------------------------------------|:-----------------------:|:------------:|:---------:|
| Composer [official website][composer] | [composer](composer.md) |   required   |     ❌     |

## Available since v1.3.0

All bundled converters support now to include the SARIF [versionControlProvenance property][versionControlProvenance].

You have just to specify an existing directory (local git repository) containing files to scan
with the `--project-dir` option of `convert` ConsoleTool command, if it's not the current working directory.

> [!IMPORTANT]
>
> If `--project-dir` did not provide a valid GIT folder (local repository), then this option is purely ignored
> and no message (error/warning) is displayed

For example:

```json
{
    "$schema": "https://json.schemastore.org/sarif-2.1.0.json",
    "version": "2.1.0",
    "runs": [
        {

            "invocations": [
                {
                    "executionSuccessful": true,
                    "commandLine": "./report-converter",
                    "arguments": [
                        "convert",
                        "phpcs-fixer",
                        "--input-format=checkstyle",
                        "--input-file=examples/phpcs-fixer/checkstyle.xml",
                        "--output-file=.sarif.json",
                        "--project-dir=.",
                        "-v"
                    ],
                    "workingDirectory": {
                        "uri": "file:///shared/backups/bartlett/sarif-php-converters/"
                    }
                }
            ],
            "versionControlProvenance": [
                {
                    "repositoryUri": "https://github.com/llaville/sarif-php-converters.git",
                    "revisionId": "0a395a180d1d836702f50de1b32bac60c663aee2",
                    "branch": "1.3",
                    "revisionTag": "1.2.0-14-g0a395a1",
                    "mappedTo": {
                        "uriBaseId": "SOURCE_ROOT"
                    },
                    "properties": {
                        "versionControl.system": "git",
                        "versionControl.version": "2.39.5"
                    }
                }
            ]

        }
    ]
}
```

[composer]: https://github.com/composer/composer
[ecs]: https://github.com/easy-coding-standard/easy-coding-standard
[phan]: https://github.com/phan/phan
[phpcs]: https://github.com/PHPCSStandards/PHP_CodeSniffer
[phpcs-fixer]: https://github.com/PHP-CS-Fixer/PHP-CS-Fixer
[phplint]: https://github.com/overtrue/phplint
[phpmd]: https://github.com/phpmd/phpmd
[phpstan]: https://github.com/phpstan/phpstan
[rector]: https://github.com/rectorphp/rector
[twigcs-fixer]: https://github.com/VincentLanglet/Twig-CS-Fixer
[versionControlProvenance]: https://docs.oasis-open.org/sarif/sarif/v2.1.0/errata01/os/sarif-v2.1.0-errata01-os-complete.html#_Toc141790747
