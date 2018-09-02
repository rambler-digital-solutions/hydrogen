<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Processor\DatabaseProcessor;

use Doctrine\ORM\QueryBuilder;
use RDS\Hydrogen\Criteria\CriterionInterface;
use RDS\Hydrogen\Criteria\Limit;

/**
 * Class LimitBuilder
 */
class LimitBuilder extends Builder
{
    /**
     * @param QueryBuilder $builder
     * @param CriterionInterface|Limit $limit
     * @return \Generator
     */
    public function apply($builder, CriterionInterface $limit): \Generator
    {
        $builder->setMaxResults($limit->getLimit());

        yield from $this->nothing();
    }
}
