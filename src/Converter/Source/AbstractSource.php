<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Converter\Source;

use Bartlett\Sarif\Contract\NormalizerInterface;
use Bartlett\Sarif\Contract\SourceInterface;

use AppendIterator;
use Iterator;
use function end;
use function explode;
use function get_class;
use function in_array;
use function rtrim;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
abstract class AbstractSource implements SourceInterface
{
    /**
     * @var NormalizerInterface[] $normalizers
     */
    private array $normalizers;
    private AppendIterator $normalized;

    /**
     * @param iterable<NormalizerInterface> $normalizers
     */
    public function __construct(iterable $normalizers = [])
    {
        $this->normalizers = $normalizers;
        $this->normalized = new AppendIterator();
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        $names = explode("\\", get_class($this));
        return rtrim(end($names), 'Source');
    }

    /**
     * @inheritDoc
     */
    public function getFiles(): Iterator
    {
        foreach ($this->getAttribute('files') as $files) {
            foreach ($files as $file) {
                yield $file;
            }
        };
    }

    /**
     * @inheritDoc
     */
    public function getErrors(): Iterator
    {
        foreach ($this->getAttribute('errors') as $errors) {
            foreach ($errors as $file => $error) {
                yield $file => $error;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getRules(): Iterator
    {
        foreach ($this->getAttribute('rules') as $rules) {
            foreach ($rules as $ruleId => $ruleDef) {
                yield $ruleId => $ruleDef;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getAttribute(string $name): Iterator
    {
        foreach ($this->normalized->getArrayIterator() as $iterator) {
            if ($iterator->offsetExists($name)) {
                yield $iterator->offsetGet($name);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function normalize($data, string $format, array $context): bool
    {
        // If a normalizer supports the given format, use it
        $normalizer = $this->getNormalizer($format);
        if ($normalizer instanceof NormalizerInterface) {
            $normalized = $normalizer->normalize($data, $format, $context);
            $this->normalized->append($normalized->getIterator());
            return true;
        }
        return false;
    }

    /**
     * Returns a matching normalizer.
     */
    protected function getNormalizer(string $format): ?NormalizerInterface
    {
        foreach ($this->normalizers as $normalizer) {
            if (!$normalizer instanceof NormalizerInterface) {
                // skip invalid normalizer item
                continue;
            }

            if (in_array($format, $normalizer->getSupportedFormats())) {
                return $normalizer;
            }
        }

        return null;
    }
}
