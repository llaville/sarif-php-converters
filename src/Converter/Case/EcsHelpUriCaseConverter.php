<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Converter\Case;

use Bartlett\Sarif\Contract\HelpUriCaseConverterInterface;

use Jawira\CaseConverter\CaseConverter;
use Jawira\CaseConverter\CaseConverterInterface;

use function count;
use function end;
use function explode;
use function sprintf;
use function strlen;
use function substr;

/**
 * @author Laurent Laville
 * @since Release 1.5.0
 */
final class EcsHelpUriCaseConverter implements HelpUriCaseConverterInterface
{
    public const URI_PATTERN_PHPCS = 'https://github.com/PHPCSStandards/PHP_CodeSniffer/wiki/Customisable-Sniff-Properties#%s';
    public const URI_PATTERN_PHPCS_FIXER = 'https://cs.symfony.com/doc/rules/%s/%s.html';
    public const URI_PATTERN_SYMPLIFY = 'https://github.com/symplify/coding-standard/blob/main/docs/rules_overview.md#%s';
    public const URI_PATTERN_SLEVOMAT = 'https://github.com/slevomat/coding-standard/blob/master/doc/%s.md#%s-';

    private CaseConverterInterface $caseConverter;

    public function __construct(?CaseConverterInterface $caseConverter = null)
    {
        $this->caseConverter = $caseConverter ?? new CaseConverter();
    }

    public function convert(string $checkerClass): string
    {
        $nameParts = explode('\\', $checkerClass);

        return match ($nameParts[0]) {
            'PHP_CodeSniffer' => $this->fromPhpCodeSniffer($nameParts),
            'PhpCsFixer' => $this->fromPhpCsFixer($nameParts),
            'Symplify' => $this->fromSymplify($nameParts),
            'SlevomatCodingStandard' => $this->fromSlevomat($nameParts),
            default => '',
        };
    }

    /**
     * Help URI detection for PHP_CodeSniffer, based on sniff class name.
     *
     * e.g: PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\ArrayIndentSniff
     *
     * @param string[] $nameParts
     */
    private function fromPhpCodeSniffer(array $nameParts): string
    {
        $standard = $nameParts[count($nameParts) - 4];  // e.g: Generic
        $group = $nameParts[count($nameParts) - 2];  // e.g: Arrays
        $name = substr(end($nameParts), 0, -strlen('Sniff'));  // e.g: ArrayIndent
        $name = $standard . $group . $name;

        return sprintf(
            self::URI_PATTERN_PHPCS,
            $this->caseConverter->convert($name)->toLower()
        );
    }

    /**
     * Help URI detection for PHP-CS-Fixer, based on fixer class name.
     *
     * e.g: PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer
     *
     * @param string[] $nameParts
     */
    private function fromPhpCsFixer(array $nameParts): string
    {
        $group = $nameParts[count($nameParts) - 2];  // e.g: Whitespace
        $name = substr(end($nameParts), 0, -strlen('Fixer'));  // e.g: NoExtraBlankLines

        return sprintf(
            self::URI_PATTERN_PHPCS_FIXER,
            $this->caseConverter->convert($group)->toSnake(),
            $this->caseConverter->convert($name)->toSnake()
        );
    }

    /**
     * Help URI detection for PHP-CS-Fixer Simplify Coding Standard, based on fixer class name.
     *
     * e.g: Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer
     *
     * @param string[] $nameParts
     */
    private function fromSymplify(array $nameParts): string
    {
        $name = end($nameParts);  // e.g: ArrayOpenerAndCloserNewlineFixer

        return sprintf(
            self::URI_PATTERN_SYMPLIFY,
            $this->caseConverter->convert($name)->toLower()
        );
    }

    /**
     * Help URI detection for PHP-CS-Fixer Slevomat Coding Standard, based on fixer class name.
     *
     * e.g: SlevomatCodingStandard\Sniffs\Arrays\TrailingArrayCommaSniff
     *
     * @param string[] $nameParts
     */
    private function fromSlevomat(array $nameParts): string
    {
        $namespace = $nameParts[count($nameParts) - 4];  // e.g: SlevomatCodingStandard
        $group = $nameParts[count($nameParts) - 2];  // e.g: Arrays
        $name = substr(end($nameParts), 0, -strlen('Sniff'));  // e.g: TrailingArrayComma
        $name = $namespace . $group . $name;

        return sprintf(
            self::URI_PATTERN_SLEVOMAT,
            $this->caseConverter->convert($group)->toLower(),
            $this->caseConverter->convert($name)->toLower()
        );
    }
}
