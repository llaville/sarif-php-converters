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
use function in_array;
use function json_decode;
use function sprintf;
use function strtolower;

/**
 * @extends AbstractNormalizer<mixed>
 * @author Laurent Laville
 * @since Release 1.2.0
 */
final class ComposerNormalizer extends AbstractNormalizer
{
    public function getSupportedFormats(): array
    {
        return [
            NormalizerInterface::FORMAT_JSON,
        ];
    }

    public function normalize($data, string $format, array $context): ?ArrayObject
    {
        if (!in_array($format, $this->getSupportedFormats())) {
            return null;
        }

        // default common mapping for all formats
        $mapping = [
            'cve' => 'ReportingDescriptor.id',
            'severity' => 'Result.level',
            'affectedVersions' => 'Result.properties.affectedVersions',
            'reportedAt' => 'Result.properties.reportedAt',
        ];

        return new ArrayObject($this->fromJson($data, $mapping));
    }

    private function fromJson(string $data, array $mapping): array
    {
        // $data is already JSON encoded
        $collected = json_decode($data, true);

        $errors = [];
        $rules = [];

        foreach ($collected['advisories'] ?? [] as $package => $advisories) {
            foreach ($advisories as $advisory) {
                $attributes = [
                    'Result.properties.packageName' => $package,
                    'Result.properties.AdvisoryId' => [],
                ];
                foreach ($advisory as $attr => $value) {
                    if ('advisoryId' === $attr) {
                        $attributes['Result.properties.AdvisoryId']['PKSA'] = [
                            'id' => $value,
                            'link' => sprintf('https://packagist.org/security-advisories/%s', $value),
                        ];
                    }
                    if ('sources' === $attr) {
                        foreach ($value as $source) {
                            if ('GitHub' === $source['name']) {
                                $attributes['Result.properties.AdvisoryId']['GHSA'] = [
                                    'id' => $source['remoteId'],
                                    'link' => sprintf('https://github.com/advisories/%s', $source['remoteId']),
                                ];
                                break;
                            }
                        }
                    }
                    if ('title' === $attr) {
                        $attributes['Result.message'] = "Found '{0}' vulnerability in '{1}'";
                        $attributes['Result.message.arguments'] = [$advisory['cve'], $package];
                    }
                    $key = $mapping[$attr] ?? null;
                    if (null === $key) {
                        // skips unsolicited entry
                        continue;
                    }
                    $newValue = $value;
                    if ('Result.level' === $key) {
                        // match severity value with standard SARIF level enum
                        $newValue = $this->severityMap($value);
                    }
                    if ('ReportingDescriptor.id' === $key) {
                        if (!isset($rules[$value])) {
                            $rules[$value] = [
                                'fullDescription' => $advisory['title'],
                                'helpUri' => 'https://www.cve.org/CVERecord?id=' . $value,
                                'properties' => [
                                    'frequency' => 0,
                                ]
                            ];
                            $attributes['Result.properties.AdvisoryId']['CVE'] = [
                                'id' => $value,
                                'link' => 'https://www.cve.org/CVERecord?id=' . $value,
                            ];
                        }
                        $rules[$value]['properties']['frequency'] += 1;
                        $rules[$value]['defaultConfiguration']['enabled'] = !isset($advisory['ignoreReason']);
                        $rules[$value]['defaultConfiguration']['level'] = $this->severityMap($advisory['severity']);
                    }
                    $attributes[$key] = $newValue;
                }
                $errors['composer.json'][] = $attributes;
            }
        }

        return [
            'errors' => $errors,
            'rules' => $rules,
        ];
    }

    private function severityMap(string $severity): string
    {
        return match (strtolower($severity)) {
            'medium' => 'note',
            'high' => 'warning',
            'critical' => 'error',
            default => 'none',
        };
    }
}
