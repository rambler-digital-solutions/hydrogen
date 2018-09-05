<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Processor;

use RDS\Hydrogen\Criteria\CriterionInterface;

/**
 * Interface BuilderInterface
 */
interface BuilderInterface
{
    /**
     * @param $context
     * @param CriterionInterface $criterion
     * @return iterable|null
     */
    public function apply($context, CriterionInterface $criterion): ?iterable;
}
