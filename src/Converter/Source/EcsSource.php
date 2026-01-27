<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Converter\Source;

use Bartlett\Sarif\Contract\HelpUriCaseConverterInterface;
use Bartlett\Sarif\Converter\Case\EcsHelpUriCaseConverter;
use Bartlett\Sarif\Converter\Normalizer\EcsNormalizer;

use Iterator;
use function preg_match;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
final class EcsSource extends AbstractSource
{
    private HelpUriCaseConverterInterface $helpUriCaseConverter;

    public function __construct(iterable $normalizers = [], ?HelpUriCaseConverterInterface $helpUriCaseConverter = null)
    {
        if (empty($normalizers)) {
            $normalizers = [new EcsNormalizer()];
        }
        $this->helpUriCaseConverter = $helpUriCaseConverter ?? new EcsHelpUriCaseConverter();
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
            $ruleDef['helpUri'] = $this->helpUriCaseConverter->convert($appliedCheckerClass);
            yield $ruleId => $ruleDef;
        }
    }
}
