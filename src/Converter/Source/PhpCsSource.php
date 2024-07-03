<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Converter\Source;

use Bartlett\Sarif\Converter\Normalizer\CheckstyleNormalizer;
use Bartlett\Sarif\Converter\Normalizer\PhpCsNormalizer;

use RuntimeException;
use function file_exists;
use function file_get_contents;
use function sprintf;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
final class PhpCsSource extends AbstractSource
{
    public function __construct(iterable $normalizers = [])
    {
        if (empty($normalizers)) {
            $normalizers = [new PhpCsNormalizer()];
        }
        parent::__construct($normalizers);
    }

    public static function fromFile(string $filename, string $format): self
    {
        if (!file_exists($filename)) {
            throw new RuntimeException(sprintf('File "%s" not found', $filename));
        }

        $normalizers = [];

        if ('checkstyle' === $format) {
            $normalizers[] = new CheckstyleNormalizer();
        }

        $source = new self($normalizers);
        $source->normalize(file_get_contents($filename), $format, []);
        return $source;
    }
}
