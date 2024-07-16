<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Converter\Normalizer;

use Bartlett\Sarif\Contract\NormalizerInterface;

use TwigCsFixer\Report\Violation;

use ArrayObject;
use function in_array;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
final class TwigCsFixerNormalizer implements NormalizerInterface
{

    public function getSupportedFormats(): array
    {
        return [
            NormalizerInterface::FORMAT_INTERNAL,
        ];
    }

    public function normalize($data, string $format, array $context): ?ArrayObject
    {
        if (!in_array($format, $this->getSupportedFormats())) {
            return null;
        }

        // internal format (legacy)
        return new ArrayObject($this->fromInternal($data, $context));
    }

    /**
     * @param array<string, Violation> $data
     * @param array<string, mixed> $context Options available to the normalizer
     * @return array{
     *     files: string[],
     *     errors: array<string, array<int, array<string, bool|int|string|null>>>,
     *     rules: array<string, mixed>
     * }
     */
    private function fromInternal(array $data, array $context): array
    {
        $files = [];
        $errors = [];
        $rules = [];

        foreach ($data as $filename => $fileViolations) {
            $files[] = $filename;

            foreach ($fileViolations as $violation) {
                $ruleName = $violation->getRuleName();
                $rules[$ruleName] = [];

                $level = strtolower(Violation::getLevelAsString($violation->getLevel()));
                if ('notice' == $level) {
                    // @link (sarif-php-sdk) src/Property/Level.php
                    $level = 'note';
                }
                if ('fatal' == $level) {
                    // @link (sarif-php-sdk) src/Property/Level.php
                    $level = 'error';
                }

                $fingerprint = $violation->getIdentifier() ? $violation->getIdentifier()->toString() : $ruleName;

                $attributes = [
                    'ReportingDescriptor.id' => $ruleName,
                    'Result.fingerprint' => $fingerprint,
                    'Result.message' => $violation->getMessage(),
                    'Result.level' => $level,
                    'Region.startLine' => $violation->getLine(),
                    'Region.startColumn' => $violation->getLinePosition(),
                ];
                $errors[$filename][] = $attributes;
            }
        }

        return [
            'files' => $files,
            'errors' => $errors,
            'rules' => $rules,
        ];
    }
}
