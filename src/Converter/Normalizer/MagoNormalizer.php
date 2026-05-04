<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Converter\Normalizer;

use Bartlett\Sarif\Contract\NormalizerInterface;

use ArrayObject;
use function array_unique;

/**
 * @extends AbstractNormalizer<string>
 * @author Laurent Laville
 * @since Release 1.6.0
 */
final class MagoNormalizer extends AbstractNormalizer
{
    public function getSupportedFormats(): array
    {
        return [
            NormalizerInterface::FORMAT_SARIF,
            NormalizerInterface::FORMAT_GITLAB,
        ];
    }

    public function normalize($data, string $format, array $context): ?ArrayObject
    {
        if (!$this->isSupportedFormat($format)) {
            return null;
        }

        if (NormalizerInterface::FORMAT_SARIF === $format) {
            $innerNormalizer = new SarifNormalizer();
            return $innerNormalizer->normalize($data, $format, $context);
        }

        // default (best) base format for Mago (GitLab)
        return new ArrayObject($this->fromGitLab($data, $context));
    }

    /**
     * @param string $data
     * @param array<string, mixed> $context Options available to the normalizer
     * @param array{}|array<string, string> $mapping From-To convert's mapping
     * @return array{files: mixed, errors: mixed, rules: mixed}
     */
    protected function fromGitLab($data, array $context, array $mapping = []): array
    {
        $mapping = [
            'severity' => 'Result.level',
            'message' => 'Result.message',
        ];

        // $data is already JSON encoded
        $collected = json_decode($data, true);

        $files = [];
        $errors = [];
        $rules = [];

        foreach ($collected as $file) {
            $attributes = [
                'Region.startLine' => $file['location']['positions']['begin']['line'] ?? null,
                'Region.endLine' => $file['location']['positions']['end']['line'] ?? null,

                'Region.startColumn' => $file['location']['positions']['begin']['column'] ?? null,
                'Region.endColumn' => $file['location']['positions']['end']['column'] ?? null,
            ];

            $filename = $file['location']['path'];
            $files[] = $filename;

            $severity = $file['severity'] ?? 'none';
            if ($severity === 'minor') {
                $attributes[$mapping['severity']] = 'warning';
            }
            if ($severity === 'major') {
                $attributes[$mapping['severity']] = 'error';
            }

            $ruleId = $file['check_name'] ?? null;
            $ruleDef = [];

            $attributes[$mapping['message']] = $file['description'];

            if (!empty($ruleId)) {
                $attributes['ReportingDescriptor.id'] = $ruleId;
            }

            if (!isset($rules[$ruleId])) {
                $ruleDef['properties']['frequency'] = 0;
                $rules[$ruleId] = $ruleDef;
            }
            // provide same information that the 'lint --reporting-format code-count' command
            $rules[$ruleId]['properties']['frequency'] += 1;

            $errors[$filename][] = $attributes;
        }

        return [
            'files' => array_unique($files),
            'errors' => $errors,
            'rules' => $rules,
        ];
    }
}
