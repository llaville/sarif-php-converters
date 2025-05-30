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
use function count;
use function in_array;
use function json_decode;
use function sprintf;
use function str_pad;
use function strtolower;
use const STR_PAD_LEFT;

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

    /**
     * @param array{cve: string, severity: string, affectedVersions: string, reportedAt: string} $mapping
     * @return array<string, mixed>
     */
    private function fromJson(string $data, array $mapping): array
    {
        // $data is already JSON encoded
        $collected = json_decode($data, true);

        $errors = [];
        $rules = [];
        $ruleIdentifiers = [];

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
                        $attributes['Result.message'] = 'Found security advisory';
                        $attributes['Result.message.id'] = 'default';
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
                        $ruleIdentifiers[] = $value;
                        $caId = str_pad((string) count($ruleIdentifiers), 4, '0', STR_PAD_LEFT);
                        $newValue = $ruleId = 'CA' . $caId;
                        if (!isset($rules[$ruleId])) {
                            $rules[$ruleId] = [
                                'messageStrings' => ['default' => "Found '{0}' vulnerability in '{1}'."],
                                'shortDescription' => $advisory['title'],
                                'fullDescription' => $advisory['title'],
                                'help' => 'Learn more about CVE Record Information by following help link',
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
                        $rules[$ruleId]['name'] = 'ComposerAudit'. $caId;
                        $rules[$ruleId]['properties']['frequency'] += 1;
                        $rules[$ruleId]['defaultConfiguration']['enabled'] = !isset($advisory['ignoreReason']);
                        $rules[$ruleId]['defaultConfiguration']['level'] = $this->severityMap($advisory['severity']);
                    }
                    $attributes[$key] = $newValue;
                }
                $attributes['Region.startLine'] = 1;
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
