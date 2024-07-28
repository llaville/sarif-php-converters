<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Converter\Normalizer;

use PHPStan\Command\AnalysisResult;

use ArrayObject;
use function array_unique;
use function in_array;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
final class PhpStanNormalizer extends AbstractNormalizer
{
    public function normalize($data, string $format, array $context): ?ArrayObject
    {
        if (!in_array($format, $this->getSupportedFormats())) {
            return null;
        }

        if (!$data instanceof AnalysisResult) {
            return null;
        }

        // internal format (legacy)
        return new ArrayObject($this->fromInternal($data, $context));
    }

    /**
     * @inheritDoc
     */
    protected function fromInternal($data, array $context, array $mapping = []): array
    {
        $files = [];
        $errors = [];

        $analysisResult = $data;

        $ruleId = $context['rulePrefix'] . '101';

        foreach ($analysisResult->getFileSpecificErrors() as $fileSpecificError) {
            $file = $fileSpecificError->getFile();
            $files[] = $file;

            $attributes = [
                'ReportingDescriptor.id' => $ruleId,
                'Result.fingerprint' => $fileSpecificError->getIdentifier(),
                'Result.message' => $fileSpecificError->getMessage(),
                'Region.startLine' => $fileSpecificError->getLine(),
                'Result.properties.ignorable' => $fileSpecificError->canBeIgnored(),
            ];

            $errors[$file][] = $attributes;
        }

        return [
            'files' => array_unique($files),
            'errors' => $errors,
            'rules' => [
                $ruleId => [
                    'shortDescription' => 'Analysis error',
                    'fullDescription' => 'Errors detected during analysis of source files',
                    'help' => 'https://phpstan.org/user-guide/command-line-usage',
                    'helpUri' => 'https://phpstan.org/user-guide/getting-started',
                ],
            ],
        ];
    }
}
