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
use SimpleXMLElement;
use function extension_loaded;
use function is_string;
use function json_decode;
use function json_encode;
use const LIBXML_NOCDATA;

/**
 * @extends AbstractNormalizer<string>
 * @author Laurent Laville
 * @since Release 1.0.0
 */
final class SimpleXmlNormalizer extends AbstractNormalizer
{
    public function getSupportedFormats(): array
    {
        return [
            NormalizerInterface::FORMAT_CHECKSTYLE,
            NormalizerInterface::FORMAT_XML,
        ];
    }

    public function normalize($data, string $format, array $context): ?ArrayObject
    {
        if (!$this->isSupportedFormat($format) || !is_string($data)) {
            return null;
        }

        return new ArrayObject($this->fromXml($data));
    }

    /**
     * @return array<string, mixed>
     */
    private function fromXml(string $data): array
    {
        if (!extension_loaded('simplexml') || !extension_loaded('libxml')) {
            return [];
        }

        $sxe = simplexml_load_string($data, SimpleXMLElement::class, LIBXML_NOCDATA);
        $json = json_encode($sxe);
        return json_decode($json, true);
    }
}
