<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Query;

use RDS\Hydrogen\Criteria\Relation;
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
        foreach ($relations as $relation) {
            if (\is_string($relation)) {
                $this->add(new Relation($relation, $this));
                continue;
            }

            if (\is_array($relation)) {
                foreach ($relation as $rel => $sub) {
                    \assert(\is_string($rel) && $sub instanceof \Closure);

                    $this->add(new Relation($rel, $this, $sub));
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
