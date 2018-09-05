<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use RDS\Hydrogen\Fn\FieldFunction;
use RDS\Hydrogen\Fn\RawFunction;

/**
 * Class Bootstrap
 */
class Bootstrap
{
    /**
     * @var string[]|FunctionNode[]
     */
    private const DQL_FUNCTIONS = [
        'RAW'   => RawFunction::class,
        'FIELD' => FieldFunction::class,
    ];

    /**
     * @param EntityManagerInterface $em
     * @return void
     */
    public function register(EntityManagerInterface $em): void
    {
        $this->registerDQLFunctions($em->getConfiguration());
    }

    /**
     * @param Configuration $config
     * @return void
     */
    private function registerDQLFunctions(Configuration $config): void
    {
        foreach (self::DQL_FUNCTIONS as $name => $fn) {
            if (! $config->getCustomStringFunction($name)) {
                $config->addCustomStringFunction($name, $fn);
            }
        }
    }
}
