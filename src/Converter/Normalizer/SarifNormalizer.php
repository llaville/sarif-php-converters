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
use function in_array;
use function is_string;
use function json_decode;
use function preg_match_all;
use function str_replace;
use function strlen;
use function strpos;
use function substr;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
final class SarifNormalizer implements NormalizerInterface
{
    public function getSupportedFormats(): array
    {
        return [
            NormalizerInterface::FORMAT_SARIF,
        ];
    }

    public function normalize($data, string $format, array $context): ?ArrayObject
    {
        if (!in_array($format, $this->getSupportedFormats()) || !is_string($data)) {
            return null;
        }

        return new ArrayObject($this->fromJson($data));
    }

    /**
     * @return array{files: mixed, errors: mixed, rules: mixed}
     */
    private function fromJson(string $data): array
    {
        // $data is already JSON encoded
        $collected = json_decode($data, true);

        $files = [];
        $errors = [];
        $rules = [];

        foreach ($collected['runs'] as $run) {
            foreach ($run['tool']['driver']['rules'] as $rule) {
                $ruleId = $rule['id'];
                unset($rule['id']);
                $rule['shortDescription'] = $rule['shortDescription']['text'];
                foreach ($rule['messageStrings'] as $key => $values) {
                    $rule['messageStrings'][$key] = $this->sarif2015($values['text']);
                }
                $rules[$ruleId] = $rule;
            }

            foreach ($run['results'] as $result) {
                $physicalLocation = $result['locations'][0]['physicalLocation'];
                $filename = $this->pathToArtifactLocation($physicalLocation['artifactLocation']['uri']);

                $files[] = $filename;

                $attributes = [
                    'ReportingDescriptor.id' => $result['ruleId'],

                    'Result.message' => $result['message']['text'],
                    'Result.message.id' => $result['message']['id'],
                    'Result.message.arguments' => $result['message']['arguments'],

                    'Region.startLine' => $physicalLocation['region']['startLine'],
                    'Region.endLine' => $physicalLocation['region']['endLine'],
                ];
                $errors[$filename][] = $attributes;
            }
        }

        return [
            'files' => array_unique($files),
            'errors' => $errors,
            'rules' => $rules,
        ];
    }

    private function pathToArtifactLocation(string $path): string
    {
        if (strpos($path, 'file://') !== false) {
            $path = substr($path, strlen('file://'));
        }
        return $path;
    }


    /**
     * SARIF2015:
     * Place dynamic content in single quotes to set it off from the static text and to make it easier to spot.
     * It's especially helpful when the dynamic content is a string that might contain spaces,
     * and most especially when the string might be empty (and so would be invisible if it weren't for the quotes).
     * We recommend single quotes for a less cluttered appearance, even though US English usage would require double quotes.
     * This is part of a set of authoring practices that make your rule messages more readable, understandable, and actionable.
     *
     * See also 'SARIF2001.TerminateMessagesWithPeriod' and 'SARIF2014.ProvideDynamicMessageContent'.
     */
    private function sarif2015(string $text): string
    {
        if (preg_match_all('/\{\d\}/', $text, $matches) === false) {
            // none dynamic context found. Do nothing
            return $text;
        }

        $replaces = [];
        foreach ($matches[0] as $match) {
            $replaces[] = "'$match'";
        }
        return str_replace($matches[0], $replaces, $text);
    }
}
