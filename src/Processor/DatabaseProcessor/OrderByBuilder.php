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
use RDS\Hydrogen\Criteria\OrderBy;

/**
 * Class OrderByBuilder
 */
class OrderByBuilder extends Builder
{
    /**
     * @param QueryBuilder $builder
     * @param CriterionInterface|OrderBy $orderBy
     * @return \Generator
     */
    public function apply($builder, CriterionInterface $orderBy): \Generator
    {
        $builder->orderBy(yield $orderBy->getField(), $orderBy->getDirection());
    }
}
