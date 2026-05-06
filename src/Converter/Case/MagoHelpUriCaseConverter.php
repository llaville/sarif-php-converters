<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Converter\Case;

use Bartlett\Sarif\Contract\HelpUriCaseConverterInterface;

use Bartlett\Sarif\Converter\AbstractConverter;

use function call_user_func;
use function exec;
use function implode;
use function is_callable;
use function json_decode;
use function preg_match_all;
use function sprintf;
use function str_replace;
use function strtolower;
use const PHP_EOL;

/**
 * @author Laurent Laville
 * @since Release 1.6.0
 */
final class MagoHelpUriCaseConverter implements HelpUriCaseConverterInterface
{
    public const URI_PATTERN = 'https://mago.carthage.software/{$tool_driver_version}/en/tools/linter/rules/#%s';

    /**
     * Mapping placeholders allowed with their callback associated function.
     *
     * @var array<string, string>
     */
    private static array $placeholders = [
        'tool_driver_version' => AbstractConverter::class . '::getVersion',
    ];

    public function convert(string $checkerClass): string
    {
        if (empty($checkerClass)) {
            return '';
        }

        $uri = sprintf(self::URI_PATTERN, strtolower($checkerClass));

        $placeholders = $this->findAllPlaceholders($uri);
        foreach ($placeholders as $placeholder) {
            $uri = str_replace('{$' . $placeholder . '}', $this->replace($placeholder), $uri);
        }

        return $uri;
    }

    /**
     * @return array{}|array<string, array<string, mixed>>
     */
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

    /**
     * @return string[]
     */
    private function findAllPlaceholders(string $uri): array
    {
        $placeholders = [];
        $matches      = [];

        if (preg_match_all('#{\$([a-z_]+(\|[a-z\-]+:.*)?)}#iU', $uri, $matches)) {
            foreach ($matches[1] as $match) {
                $placeholders[] = $match;
            }
        }

        return $placeholders;
    }

    private function replace(string $placeholder): string
    {
        if (self::$placeholders[$placeholder] === null) {
            return '';
        }
        if (!is_callable(self::$placeholders[$placeholder], false)) {
            return '';
        }
        return call_user_func(self::$placeholders[$placeholder]);
    }
}
