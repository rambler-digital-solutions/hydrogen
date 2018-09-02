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
use RDS\Hydrogen\Criteria\Selection;

/**
 * Class SelectBuilder
 */
class SelectBuilder extends Builder
{
    /**
     * @param QueryBuilder $builder
     * @param CriterionInterface|Selection $select
     * @return \Generator
     */
    public function apply($builder, CriterionInterface $select): \Generator
    {
        $field = yield $select->getField();

        if ($select->hasAlias()) {
            return $builder->addSelect(\sprintf('%s AS %s', $field, $select->getAlias()));
        }

        return $builder->addSelect($field);
    }
}
