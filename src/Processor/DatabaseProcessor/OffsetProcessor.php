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
use RDS\Hydrogen\Criteria\Offset;

/**
 * Class OffsetProcessor
 */
class OffsetProcessor extends CriterionProcessor
{
    /**
     * @param QueryBuilder $builder
     * @param CriterionInterface|Offset $offset
     * @return QueryBuilder
     */
    public function apply(QueryBuilder $builder, CriterionInterface $offset): QueryBuilder
    {
        return $builder->setFirstResult($offset->getOffset());
    }
}
