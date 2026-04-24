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

use PHP_CodeSniffer\Standards\PSR12\Sniffs\Files\FileHeaderSniff;
use PHP_CodeSniffer\Standards\PSR12\Sniffs\Files\OpenTagSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Classes\ValidClassNameSniff;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

$baseDir = dirname(__DIR__, 2);

return ECSConfig::configure()
    ->withPaths([
        $baseDir . '/examples',
        $baseDir . '/src',
        $baseDir . '/tests',
    ])

    ->withSkip([
        $baseDir . '/examples/fixtures',
        FileHeaderSniff::class,
        OpenTagSniff::class,
        ValidClassNameSniff::class,
        BlankLineAfterOpeningTagFixer::class,
        OrderedImportsFixer::class,
    ])

    // Report absolute path on filenames
    ->withRealPathReporting(true)

    // add a single rule
    ->withRules([
        NoUnusedImportsFixer::class,
    ])
;
