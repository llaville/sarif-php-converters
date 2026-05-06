<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Converter\Source;

use Bartlett\Sarif\Contract\HelpUriCaseConverterInterface;
use Bartlett\Sarif\Converter\Case\MagoHelpUriCaseConverter;
use Bartlett\Sarif\Converter\Normalizer\MagoNormalizer;

use Iterator;
use function array_shift;
use function explode;
use function in_array;
use function strcasecmp;
use function strtolower;

/**
 * @author Laurent Laville
 * @since Release 1.6.0
 */
final class MagoSource extends AbstractSource
{
    private HelpUriCaseConverterInterface $helpUriCaseConverter;

    public function __construct(iterable $normalizers = [])
    {
        if (empty($normalizers)) {
            $normalizers = [new MagoNormalizer()];
        }
        $this->helpUriCaseConverter = new MagoHelpUriCaseConverter();
        parent::__construct($normalizers);
    }

    public function getRules(): Iterator
    {
        // @phpstan-ignore method.notFound
        $rules = $this->helpUriCaseConverter->getRules();

        foreach (parent::getRules() as $ruleId => $ruleDef) {
            /**
             * Dynamically add helpUri on each rule, if not already defined
             */
            if (isset($ruleDef['helpUri'])) {
                continue;
            }

            /**
             * Use the "categories" property if already defined,
             * or fallback to retrieve by the "mago lint --list-rules --pedantic --json" command
             */
            $category = $ruleDef['properties']['categories'][0] ?? $rules[$ruleId]['category'] ?? '';

            if (!empty($category)) {
                /**
                 * The optional 'name' property ([3.49.7](https://docs.oasis-open.org/sarif/sarif/v2.1.0/os/sarif-v2.1.0-os.html#_Toc34317843))
                 * is an identifier that is understandable to an end user.
                 * Therefore if both 'id' and 'name' are present, they must be different.
                 * If they are identical, the tool must omit the 'name' property.
                 */
                $ruleName = $rules[$ruleId]['name'] ?? '';
                $ruleDef['name'] = strcasecmp($ruleName, $ruleId) === 0 ? '' : $ruleName;

                $description = $rules[$ruleId]['description'] ?? '';
                $descriptionParts = explode("\n", $description);
                $ruleDef['shortDescription'] = array_shift($descriptionParts);
                $ruleDef['fullDescription'] = $description;

                $level = strtolower($rules[$ruleId]['level']);
                $ruleDef['defaultConfiguration'] = [
                    'level' => in_array($level, ["none", "note", "warning", "error"]) ? $level : 'none',
                    'parameters' => $rules[$ruleId]['requirements']
                ];
                $ruleDef['properties']['category'] = $category;
                $ruleDef['helpUri'] = $this->helpUriCaseConverter->convert($ruleId);
            }

            yield $ruleId => $ruleDef;
        }
    }
}
