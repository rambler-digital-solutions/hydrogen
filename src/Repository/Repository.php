<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Repository;

use RDS\Hydrogen\Processor\ProcessorInterface;
use RDS\Hydrogen\Query;

/**
 * Class Repository
 *
 * @property-read Query|$this $query
 */
abstract class Repository implements ObjectRepository
{
    /**
     * @var Query
     */
    private $scope;

    /**
     * @return ProcessorInterface
     */
    abstract public function getProcessor(): ProcessorInterface;

    /**
     * @param Query|null $with
     * @return Query|$this
     */
    public function query(Query $with = null): Query
    {
        $query = $with ?? $this->scope ?? Query::new();
        $query = $query->from($this)->scope($this);

        $this->scope = null;

        return $query;
    }

    /**
     * @param Query $query
     * @return Selectable|Query|$this
     */
    public function scope(Query $query): Selectable
    {
        $this->scope = $query;

        return $this;
    }

    /**
     * @param string $method
     * @return mixed
     */
    public function __get(string $method)
    {
        return $this->$method();
    }
}
