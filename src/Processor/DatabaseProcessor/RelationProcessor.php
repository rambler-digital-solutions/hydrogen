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
use RDS\Hydrogen\Criteria\Relation;

/**
 * Class RelationProcessor
 */
class RelationProcessor extends CriterionProcessor
{
    /**
     * @var int
     */
    private static $relationId = 0;

    /**
     * @param QueryBuilder $builder
     * @param CriterionInterface|Relation $with
     * @return QueryBuilder
     */
    public function apply(QueryBuilder $builder, CriterionInterface $with): QueryBuilder
    {
        $alias = $this->alias;

        foreach ($with->getRelation()->split() as $relation) {
            $childAlias = $this->createAlias($relation->getName());

            $builder->leftJoin($relation->withAlias($alias), $childAlias);
            $builder->addSelect($childAlias);

            $alias = $childAlias;
        }

        $this->processor->apply($builder, $with->getQuery(), $alias);

        return $builder;
    }

    /**
     * @param string $class
     * @return string
     */
    private function createAlias(string $class): string
    {
        return 'ref_' . \snake_case(\class_basename($class)) . '_' . ++self::$relationId;
    }
}
