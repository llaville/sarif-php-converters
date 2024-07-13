<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Console;

use Composer\InstalledVersions;

use Symfony\Component\Console\Application as BaseApplication;

use OutOfBoundsException;
use function sprintf;
use function substr;

/**
 * A console application tool to convert different error reporting standards.
 *
 * @author Laurent Laville
 * @since Release 1.0.0
 */
final class Application extends BaseApplication
{
    public const APPLICATION_NAME = 'SARIF PHP Converters';
    public const PACKAGE_NAME = 'bartlett/sarif-php-converters';

    public function __construct(string $name = 'UNKNOWN', string $version = 'UNKNOWN')
    {
        if ('UNKNOWN' === $name) {
            $name = self::APPLICATION_NAME;
        }
        if ('UNKNOWN' === $version) {
            $version = self::getPrettyVersion();
        }
        parent::__construct($name, $version);
    }

    public static function getPrettyVersion(): string
    {
        foreach (InstalledVersions::getAllRawData() as $installed) {
            if (!isset($installed['versions'][self::PACKAGE_NAME])) {
                continue;
            }

            $version = $installed['versions'][self::PACKAGE_NAME]['pretty_version']
                ?? $installed['versions'][self::PACKAGE_NAME]['version']
                ?? 'dev'
            ;

            $aliases = $installed['versions'][self::PACKAGE_NAME]['aliases'] ?? [];

            return sprintf(
                '%s@%s',
                $aliases[0] ?? $version,
                substr(InstalledVersions::getReference(self::PACKAGE_NAME), 0, 7)
            );
        }

        throw new OutOfBoundsException(sprintf('Package "%s" is not installed', self::PACKAGE_NAME));
    }
}
