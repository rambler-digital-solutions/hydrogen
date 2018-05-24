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
use RDS\Hydrogen\Processor\DatabaseProcessor;

/**
 * Interface DatabaseCriterionProcessor
 */
interface DatabaseCriterionProcessor
{
    /**
     * DatabaseCriterionProcessor constructor.
     * @param string $alias
     * @param DatabaseProcessor $processor
     */
    public function __construct(string $alias, DatabaseProcessor $processor);

    /**
     * @return iterable
     */
    public function getParameters(): iterable;

    /**
     * @param QueryBuilder $builder
     * @param CriterionInterface $criterion
     * @return QueryBuilder
     */
    public function apply(QueryBuilder $builder, CriterionInterface $criterion): QueryBuilder;
}
