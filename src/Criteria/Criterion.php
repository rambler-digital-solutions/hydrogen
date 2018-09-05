<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Criteria;

use RDS\Hydrogen\Criteria\Common\Field;
use RDS\Hydrogen\Query;

/**
 * Class Criterion
 */
abstract class Criterion implements CriterionInterface
{
    /**
     * @var Query
     */
    protected $query;

    /**
     * Criterion constructor.
     * @param Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * @param string $name
     * @return Field
     */
    protected function field(string $name): Field
    {
        return new Field($name);
    }

    /**
     * @param Query $query
     * @return CriterionInterface
     */
    public function attach(Query $query): CriterionInterface
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAttached(): bool
    {
        return $this->query !== null;
    }

    /**
     * @return Query
     */
    public function getQuery(): Query
    {
        return $this->query;
    }

    /**
     * @return string
     */
    public function getQueryAlias(): string
    {
        return $this->query->getAlias();
    }
}
