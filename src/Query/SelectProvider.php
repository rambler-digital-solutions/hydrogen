<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Query;

use RDS\Hydrogen\Criteria\Selection;
use RDS\Hydrogen\Query;

/**
 * Trait SelectProvider
 * @mixin Query
 */
trait SelectProvider
{
    /**
     * @param array|string ...$fields
     * @return Query|$this|self
     */
    public function select(...$fields): self
    {
        foreach ($fields as $field) {
            if (! \is_array($field) && ! \is_string($field)) {
                $error = 'Selection should be array (["field" => "alias"]) or string ("field") but %s given';
                throw new \InvalidArgumentException(\sprintf($error, \gettype($field)));
            }

            if (\is_string($field)) {
                $this->add(new Selection($field));
                continue;
            }

            foreach ($field as $name => $alias) {
                $this->add(new Selection($name, $alias));
            }
        }
        return $this;
    }
}
