<?php

declare(strict_types=1);

/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$header = <<<EOF
This file is part of the Sarif-PHP-Converters package.

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

return (new PhpCsFixer\Config())
    // allow to run on unsupported PHP Versions
    // {@link https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/blob/master/README.md#supported-php-versions}
    // available since PHP-CS-Fixer 3.76.0
    // use instead `PHP_CS_FIXER_IGNORE_ENV` env var for older versions
    ->setUnsupportedPhpVersionAllowed(true)
    // use default @link https://www.php-fig.org/per/coding-style/
    ->setRules([
        '@PSR12' => true,
        'header_comment' => ['header' => $header, 'comment_type' => 'PHPDoc'],
        'blank_line_after_opening_tag' => true,
        'braces_position' => true,
        'compact_nullable_type_declaration' => true,
        'concat_space' => ['spacing' => 'one'],
        'declare_equal_normalize' => ['space' => 'none'],
        'declare_parentheses' => true,
        'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],
        'new_with_parentheses' => true,
        'no_empty_statement' => false,
        'no_extra_blank_lines' => true,
        'no_leading_import_slash' => true,
        'no_leading_namespace_whitespace' => true,
        'no_unused_imports' => true,
        'no_whitespace_in_blank_line' => true,
        'return_type_declaration' => ['space_before' => 'none'],
        'single_space_around_construct' => true,
        'single_trait_insert_per_statement' => true,
        'type_declaration_spaces' => true,
    ])
    // default source code to scan
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in([dirname(__DIR__, 2) . '/src/'])
    )
;
