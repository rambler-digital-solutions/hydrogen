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
use RDS\Hydrogen\Criteria\Relation;

/**
 * Class RelationBuilder
 */
class RelationBuilder extends WhereBuilder
{
    /**
     * @var array
     */
    private $relations = [];

    /**
     * @param QueryBuilder $builder
     * @param CriterionInterface|Relation $relation
     * @return \Generator
     */
    public function apply($builder, CriterionInterface $relation): \Generator
    {
        $field = $relation->getField();
        $alias = $relation->getAlias();

        foreach ($field->getChunks() as $chunk) {
            if (isset($this->relations[$chunk])) {
                $alias = $this->relations[$chunk];
                continue;
            }

            $alias = $this->join($builder, $chunk, $alias);

            $this->relations[$chunk] = $alias;
        }

        yield $relation->getQuery()->withAlias($alias);
    }

    /**
     * @param QueryBuilder $builder
     * @param string $child
     * @param string $parent
     * @return string
     */
    private function join(QueryBuilder $builder, string $child, string $parent): string
    {
        $relation = Field::new($child)->withAlias($parent);
        $alias = \str_replace('.', '_', $relation->toString());

        $builder->leftJoin($relation->toString(), $alias);
        $builder->addSelect($alias);

        return $alias;
    }
}
