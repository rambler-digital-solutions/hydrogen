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
     * @return iterable|null
     */
    public function apply($builder, CriterionInterface $select): ?iterable
    {
        /** @var string $selection */
        $selection = yield $select->getField();

        if ($select->hasAlias()) {
            $selection .= ' AS ' . $select->getAlias();
        }

        $builder->addSelect($selection);
    }
}
