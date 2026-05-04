<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Converter\Case;

use Bartlett\Sarif\Contract\HelpUriCaseConverterInterface;

use function exec;
use function implode;
use function json_decode;
use function sprintf;
use function strtolower;
use const PHP_EOL;

/**
 * @author Laurent Laville
 * @since Release 1.6.0
 */
final class MagoHelpUriCaseConverter implements HelpUriCaseConverterInterface
{
    public const URI_PATTERN = 'https://mago.carthage.software/tools/linter/rules/%s#';

    public function convert(string $checkerClass): string
    {
        if (empty($checkerClass)) {
            return '';
        }

        return sprintf(self::URI_PATTERN, strtolower($checkerClass));
    }

    public function getRules(): array
    {
        $output = null;
        $status = null;
        exec(
            'vendor/bin/mago lint --list-rules --pedantic --json',
            $output,
            $status
        );
        if ($status !== 0) {
            // do not fail, if previous command ("mago") is not available
            return [];
        }

        $rules = json_decode(implode(PHP_EOL, $output), true);

        $categories = [];
        foreach ($rules as $rule) {
            $categories[$rule['code']] = $rule;
        }

        return $categories;
    }
}
