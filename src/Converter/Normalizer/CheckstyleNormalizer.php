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
use function count;
use function in_array;
use function is_string;
use function strtolower;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
final class CheckstyleNormalizer implements NormalizerInterface
{
    public function getSupportedFormats(): array
    {
        return [
            NormalizerInterface::FORMAT_CHECKSTYLE,
        ];
    }

    public function normalize($data, string $format, array $context): ?ArrayObject
    {
        if (!in_array($format, $this->getSupportedFormats()) || !is_string($data)) {
            return null;
        }

        $innerNormalizer = new SimpleXmlNormalizer();
        $collected = $innerNormalizer->normalize($data, $format, $context);

        return new ArrayObject(($this->fromSimpleXml($collected)));
    }

    /**
     * @param ArrayObject<string, mixed>|null $data Data to normalize
     * @return array<string, mixed>
     */
    private function fromSimpleXml(?ArrayObject $data): array
    {
        $mapping = [
            'line' => 'Region.startLine',
            'column' => 'Region.startColumn',
            'severity' => 'Result.level',
            'message' => 'Result.message',
            'source' => 'ReportingDescriptor.id',
        ];

        $filenames = [];
        $errors = [];
        $rules = [];

        foreach ($data['file'] as $file) {
            $filename = $file['@attributes']['name'];
            $filenames[] = $filename;

            foreach ($file['error'] as $error) {
                $attrs = count($error) > 1 ? $error : $error['@attributes'];
                $attributes = [];
                foreach ($attrs as $attribute => $value) {
                    $key = $mapping[$attribute] ?? '.' . $attribute;
                    if ('Result.level' === $key) {
                        $attributes[$key] = strtolower($value);
                        if ('info' === $attributes[$key]) {
                            // @link src/Property/Level.php
                            $attributes[$key] = 'note';
                        }
                        if ('fatal' === $attributes[$key]) {
                            // @link src/Property/Level.php
                            $attributes[$key] = 'error';
                        }
                    } else {
                        $attributes[$key] = $value;
                    }
                }

                $ruleId = $attributes['ReportingDescriptor.id'] ?? '';
                if (!empty($ruleId)) {
                    $rules[$ruleId] = [];
                }

                if (!empty($attributes)) {
                    $errors[$filename][] = $attributes;
                }
            }
        }

        return [
            'files' => array_unique($filenames),
            'errors' => $errors,
            'rules' => $rules,
        ];
    }
}
