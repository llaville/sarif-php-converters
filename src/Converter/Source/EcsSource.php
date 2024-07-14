<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Converter\Source;

use Bartlett\Sarif\Converter\Normalizer\EcsNormalizer;

use PhpCsFixer\Preg;

use Iterator;
use function count;
use function end;
use function explode;
use function mb_strtolower;
use function preg_match;
use function sprintf;
use function strlen;
use function strtolower;
use function substr;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
final class EcsSource extends AbstractSource
{
    public const URI_PATTERN_PHPCS = 'https://github.com/PHPCSStandards/PHP_CodeSniffer/wiki/Customisable-Sniff-Properties#%s';
    public const URI_PATTERN_PHPCS_FIXER = 'https://cs.symfony.com/doc/rules/%s/%s.html';
    public const URI_PATTERN_SYMPLIFY = 'https://github.com/symplify/coding-standard/blob/main/docs/rules_overview.md#%s';
    public const URI_PATTERN_SLEVOMAT = 'https://github.com/slevomat/coding-standard/blob/master/doc/%s.md#%s-';

    public function __construct(iterable $normalizers = [])
    {
        if (empty($normalizers)) {
            $normalizers = [new EcsNormalizer()];
        }
        parent::__construct($normalizers);
    }

    public function getRules(): Iterator
    {
        foreach (parent::getRules() as $ruleId => $ruleDef) {
            /**
             * Dynamically add helpUri on each rule (Fixer, Sniff), if not already defined
             */
            if (isset($ruleDef['helpUri'])) {
                continue;
            }

            // e.g: EasyCodingStandard.PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\ArrayIndentSniff.KeyIncorrect
            // e.g: EasyCodingStandard.PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer
            // e.g: EasyCodingStandard.Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer
            // e.g: EasyCodingStandard.SlevomatCodingStandard\Sniffs\Arrays\TrailingArrayCommaSniff.MissingTrailingComma

            if (preg_match('/^(EasyCodingStandard\.)([a-zA-Z\\\\]+)\.?([a-zA-Z]*)/', $ruleId, $matches) !== 1) {
                // invalid source rule pattern
                continue;
            }
            $appliedCheckerClass = $matches[2];

            $nameParts = explode('\\', $appliedCheckerClass);

            switch ($nameParts[0]) {    // standard
                case 'PHP_CodeSniffer':
                    $helpUri = $this->phpCodeSnifferChecker($nameParts);
                    break;
                case 'PhpCsFixer':
                    $helpUri = $this->phpCsFixerChecker($nameParts);
                    break;
                case 'Symplify':
                    $helpUri = $this->symplifyChecker($nameParts);
                    break;
                case 'SlevomatCodingStandard':
                    $helpUri = $this->slevomatCodingStandardChecker($nameParts);
                    break;
                default:
                    $helpUri = '';
            }

            $ruleDef['helpUri'] = $helpUri;
            yield $ruleId => $ruleDef;
        }
    }

    /**
     * Help URI detection for PHP_CodeSniffer, based on sniff class name.
     *
     * e.g: PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\ArrayIndentSniff
     *
     * @param string[] $nameParts
     */
    private function phpCodeSnifferChecker(array $nameParts): string
    {
        $standard = $nameParts[count($nameParts) - 4];  // e.g: Generic
        $group = $nameParts[count($nameParts) - 2];  // e.g: Arrays
        $name = substr(end($nameParts), 0, -strlen('Sniff'));  // e.g: ArrayIndent
        return sprintf(self::URI_PATTERN_PHPCS, strtolower($standard . $group . $name));
    }

    /**
     * Help URI detection for PHP-CS-Fixer, based on fixer class name.
     *
     * e.g: PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer
     *
     * @param string[] $nameParts
     */
    private function phpCsFixerChecker(array $nameParts): string
    {
        $group = $nameParts[count($nameParts) - 2];  // e.g: Whitespace
        $name = substr(end($nameParts), 0, -strlen('Fixer'));  // e.g: NoExtraBlankLines
        return sprintf(
            self::URI_PATTERN_PHPCS_FIXER,
            self::camelCaseToUnderscore($group),
            self::camelCaseToUnderscore($name)
        );
    }

    /**
     * Help URI detection for PHP-CS-Fixer Symplify Coding Standard, based on fixer class name.
     *
     * e.g: Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer
     *
     * @param string[] $nameParts
     */
    private function symplifyChecker(array $nameParts): string
    {
        $name = end($nameParts);  // e.g: ArrayOpenerAndCloserNewlineFixer
        return sprintf(self::URI_PATTERN_SYMPLIFY, strtolower($name));
    }

    /**
     * Help URI detection for PHP-CS-Fixer Slevomat Coding Standard, based on fixer class name.
     *
     * e.g: SlevomatCodingStandard\Sniffs\Arrays\TrailingArrayCommaSniff
     *
     * @param string[] $nameParts
     */
    private function slevomatCodingStandardChecker(array $nameParts): string
    {
        $namespace = $nameParts[count($nameParts) - 4];  // e.g: SlevomatCodingStandard
        $group = $nameParts[count($nameParts) - 2];  // e.g: Arrays
        $name = substr(end($nameParts), 0, -strlen('Sniff'));  // e.g: TrailingArrayComma
        return sprintf(self::URI_PATTERN_SLEVOMAT, strtolower($group), strtolower($namespace . $group . $name));
    }

    /**
     * Converts a camel cased string to a snake cased string.
     * @credits to PHP-CS-Fixer
     */
    private static function camelCaseToUnderscore(string $string): string
    {
        return mb_strtolower(Preg::replace('/(?<!^)(?<!_)((?=[\p{Lu}][^\p{Lu}])|(?<![\p{Lu}])(?=[\p{Lu}]))/', '_', $string));
    }
}
