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
     * @var array|string[]
     */
    private $relations = [];

    /**
     * @param QueryBuilder $builder
     * @param CriterionInterface|Relation $with
     * @return QueryBuilder
     */
    public function apply(QueryBuilder $builder, CriterionInterface $with): QueryBuilder
    {
        $alias = $this->alias;

        foreach ($with->getRelation()->split() as $relation) {
            $parent     = $relation->withAlias($alias);
            $exists     = $this->hasAlias($parent);
            $childAlias = $this->fetchAlias($parent, $relation->getName());

            if (! $exists) {
                $builder->leftJoin($parent, $childAlias);
                $builder->addSelect($childAlias);
            }

            $alias = $childAlias;
        }

        $this->processor->apply($builder, $with->getQuery(), $alias);

        return $builder;
    }

    /**
     * @param string $parent
     * @return bool
     */
    private function hasAlias(string $parent): bool
    {
        return \array_key_exists($parent, $this->relations);
    }

    /**
     * @param string $parent
     * @param string $class
     * @return string
     */
    private function fetchAlias(string $parent, string $class): string
    {
        if (! $this->hasAlias($parent)) {
            $this->relations[$parent] = $this->createAlias($class);
        }

        return $this->relations[$parent];
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
