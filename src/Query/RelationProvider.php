<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Query;

use RDS\Hydrogen\Criteria\Join;
use RDS\Hydrogen\Query;

/**
 * Trait RelationProvider
 * @mixin Query
 */
trait RelationProvider
{
    /**
     * @param string|array ...$relations
     * @return Query|$this|self
     */
    public function with(...$relations): self
    {
        return $this->addRelation(function(string $field, \Closure $inner = null) {
            // TODO
            // return new Relation($field, $this, $inner);
            return new Join($field, $this, Join::TYPE_LEFT_JOIN, $inner);
        }, ...$relations);
    }

    /**
     * @param string|array ...$relations
     * @return Query|$this|self
     */
    public function join(...$relations): self
    {
        return $this->addRelation(function(string $field, \Closure $inner = null) {
            return new Join($field, $this, Join::TYPE_JOIN, $inner);
        }, ...$relations);
    }

    /**
     * @param string|array ...$relations
     * @return Query|$this|self
     */
    public function leftJoin(...$relations): self
    {
        return $this->addRelation(function(string $field, \Closure $inner = null) {
            return new Join($field, $this, Join::TYPE_LEFT_JOIN, $inner);
        }, ...$relations);
    }

    /**
     * @param string|array ...$relations
     * @return Query|$this|self
     */
    public function innerJoin(...$relations): self
    {
        return $this->addRelation(function(string $field, \Closure $inner = null) {
            return new Join($field, $this, Join::TYPE_INNER_JOIN, $inner);
        }, ...$relations);
    }

    /**
     * @param \Closure $onCreate
     * @param string|array ...$relations
     * @return Query|$this|self
     */
    private function addRelation(\Closure $onCreate, ...$relations): self
    {
        foreach ($relations as $relation) {
            if (\is_string($relation)) {
                $this->add($onCreate($relation));
                continue;
            }

            if (\is_array($relation)) {
                foreach ($relation as $rel => $sub) {
                    \assert(\is_string($rel) && $sub instanceof \Closure);

                    $this->add($onCreate($rel, $sub));
                }
                continue;
            }

            $error = 'Relation should be string ("relation_name") '.
                'or array (["relation" => function]), ' .
                'but %s given';

            throw new \InvalidArgumentException(\sprintf($error, \gettype($relation)));
        }

        return $this;
    }
}
