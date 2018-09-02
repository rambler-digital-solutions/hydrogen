<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Processor\DatabaseProcessor;

use RDS\Hydrogen\Processor\BuilderInterface;

/**
 * Class Builder
 */
abstract class Builder implements BuilderInterface
{
    /**
     * @return \Generator
     */
    protected function nothing(): \Generator
    {
        if (false) {
            yield;
        }
    }

    /**
     * @param \Generator $generator
     * @param \Closure $then
     * @return \Generator
     */
    protected function extractResult(\Generator $generator, \Closure $then): \Generator
    {
        yield from $generator;

        $then($generator->getReturn());
    }
}
