<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Converter\Normalizer;

use Bartlett\Sarif\Contract\NormalizerInterface;

use Psalm\Plugin\EventHandler\Event\AfterAnalysisEvent;

use ArrayObject;
use function in_array;
use function sprintf;
use function strpos;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
final class PsalmNormalizer implements NormalizerInterface
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

        if (!$data instanceof AfterAnalysisEvent) {
            return null;
        }

        // internal format (legacy)
        return new ArrayObject($this->fromInternal($data, $context));
    }

    /**
     * @param array<string, mixed> $context Options available to the normalizer
     * @return array{
     *     files: string[],
     *     errors: array<string, array<int, array<string, string>>>,
     *     rules: array<string, mixed>
     * }
     */
    private function fromInternal(AfterAnalysisEvent $event, array $context): array
    {
        $files = [];
        $errors = [];
        $rules = [];

        foreach ($event->getIssues() as $filePath => $issues) {
            $files[] = $filePath;
            foreach ($issues as $issue) {
                $ruleId = sprintf('%s%003d', $context['rulePrefix'], $issue->shortcode);

                $attributes = [
                    'ReportingDescriptor.id' => $ruleId,
                    'Result.fingerprint' => $issue->type,
                    'Result.message' => $issue->message,
                    'Region.startLine' => $issue->line_from,
                    'Region.endLine' => $issue->line_to,
                    'Region.startColumn' => $issue->column_from,
                    'Region.endColumn' => $issue->column_to,
                    'Result.properties.severity' => $issue->severity,
                ];
                $errors[$filePath][] = $attributes;

                $tag = strpos($issue->type, 'Tainted') === 0 ? 'security' : 'maintainability';
                $ruleDef = [
                    'name' => $issue->type,
                    'shortDescription' => $issue->type,
                    'helpUri' => $issue->link,
                    'properties' => ['tags', [$tag]],
                ];
                $rules[$ruleId] = $ruleDef;
            }
        }

        return [
            'files' => $files,
            'errors' => $errors,
            'rules' => $rules,
        ];
    }
}
