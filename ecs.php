<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Laurent Laville
 * @since Release 1.0.0
 */

use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/examples',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])

    ->withSkip([
        __DIR__ . '/examples/fixtures',
        \PHP_CodeSniffer\Standards\PSR12\Sniffs\Files\FileHeaderSniff::class,
        \PHP_CodeSniffer\Standards\PSR12\Sniffs\Files\OpenTagSniff::class,
        \PHP_CodeSniffer\Standards\Squiz\Sniffs\Classes\ValidClassNameSniff::class,
        \PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer::class,
        \PhpCsFixer\Fixer\Import\OrderedImportsFixer::class,
    ])

    // Report absolute path on filenames
    ->withRealPathReporting(true)

    // add a single rule
    ->withRules([
        \PhpCsFixer\Fixer\Import\NoUnusedImportsFixer::class,
    ])

    // add sets - group of rules
    ->withPreparedSets(
        psr12: true,
    )
;
