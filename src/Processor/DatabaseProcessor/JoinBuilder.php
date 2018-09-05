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
use RDS\Hydrogen\Criteria\Common\Field;
use RDS\Hydrogen\Criteria\CriterionInterface;
use RDS\Hydrogen\Criteria\Join;

/**
 * Class JoinBuilder
 */
class JoinBuilder extends WhereBuilder
{
    /**
     * @var array
     */
    private $relations = [];

    /**
     * @param QueryBuilder $builder
     * @param CriterionInterface|Join $join
     * @return iterable|null
     */
    public function apply($builder, CriterionInterface $join): ?iterable
    {
        [$entity, $alias] = $this->joinAll($builder, $join);

        if ($join->hasJoinQuery()) {
            $repository = $this->processor->getProcessor($entity)->getRepository();

            $query = $this->query->create()
                ->from($repository)
                ->withAlias($alias);

            yield $join->getJoinQuery($query);
        }
    }

    /**
     * @param QueryBuilder $builder
     * @param Join $join
     * @return array
     */
    private function joinAll(QueryBuilder $builder, Join $join): array
    {
        [$alias, $relation] = [$join->getQueryAlias(), []];

        foreach ($join->getRelations($this->processor) as $isLast => $relation) {
            // Resolve relation alias
            $relationAlias = $isLast && $join->hasJoinQuery()
                ? $this->getAlias($relation)
                : $this->getCachedAlias($relation);

            // Create join
            $relationField = Field::new($relation['fieldName'])->toString($alias);
            $this->join($builder, $join, $relationField, $relationAlias);

            // Add join to selection statement
            $builder->addSelect($relationAlias);

            // Shift parent
            $alias = $relationAlias;
        }

        return [$relation['targetEntity'], $alias];
    }

    /**
     * @param QueryBuilder $builder
     * @param Join $join
     * @param string $field
     * @param string $relationAlias
     * @return void
     */
    private function join(QueryBuilder $builder, Join $join, string $field, string $relationAlias): void
    {
        switch ($join->getType()) {
            case Join::TYPE_JOIN:
                $builder->join($field, $relationAlias);
                break;

            case Join::TYPE_INNER_JOIN:
                $builder->innerJoin($field, $relationAlias);
                break;

            case Join::TYPE_LEFT_JOIN:
                $builder->leftJoin($field, $relationAlias);
                break;
        }
    }

    /**
     * @param array $relation
     * @return string
     */
    private function getKey(array $relation): string
    {
        return $relation['sourceEntity'] . '_' . $relation['targetEntity'];
    }

    /**
     * @param array $relation
     * @return string
     */
    private function getCachedAlias(array $relation): string
    {
        $key = $this->getKey($relation);

        if (! isset($this->relations[$key])) {
            return $this->relations[$key] =
                $this->query->createAlias(
                    $relation['sourceEntity'],
                    $relation['targetEntity']
                );
        }

        return $this->relations[$key];
    }

    /**
     * @param array $relation
     * @return string
     */
    private function getAlias(array $relation): string
    {
        return $this->query->createAlias(
            $relation['sourceEntity'],
            $relation['targetEntity']
        );
    }
}
