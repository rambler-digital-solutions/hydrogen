<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen;

use Illuminate\Support\Collection as BaseCollection;
use RDS\Hydrogen\Collection\HigherOrderCollectionProxy;

/**
 * @property-read HigherOrderCollectionProxy $average
 * @property-read HigherOrderCollectionProxy $avg
 * @property-read HigherOrderCollectionProxy $contains
 * @property-read HigherOrderCollectionProxy $each
 * @property-read HigherOrderCollectionProxy $every
 * @property-read HigherOrderCollectionProxy $filter
 * @property-read HigherOrderCollectionProxy $first
 * @property-read HigherOrderCollectionProxy $flatMap
 * @property-read HigherOrderCollectionProxy $keyBy
 * @property-read HigherOrderCollectionProxy $map
 * @property-read HigherOrderCollectionProxy $partition
 * @property-read HigherOrderCollectionProxy $reject
 * @property-read HigherOrderCollectionProxy $sortBy
 * @property-read HigherOrderCollectionProxy $sortByDesc
 * @property-read HigherOrderCollectionProxy $sum
 *
 * @mixin \Doctrine\Common\Collections\Collection
 */
class Collection extends BaseCollection
{
    /**
     * @param string $key
     * @return HigherOrderCollectionProxy
     * @throws \InvalidArgumentException
     */
    final public function __get($key)
    {
        if (! \in_array($key, static::$proxies, true)) {
            $error = \sprintf('Property [%s] does not exist on this collection instance.', $key);
            throw new \InvalidArgumentException($error);
        }

        return new HigherOrderCollectionProxy($this, $key);
    }

    /**
     * Adds an element at the end of the collection.
     *
     * @param mixed $element The element to add.
     *
     * @return bool Always TRUE.
     */
    public function add($element): bool
    {
        $this->push($element);

        return true;
    }

    /**
     * Clears the collection, removing all elements.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->items = [];
    }

    /**
     * Removes the element at the specified index from the collection.
     *
     * @param string|int $key The kex/index of the element to remove.
     *
     * @return mixed The removed element or NULL, if the collection did not contain the element.
     */
    public function remove($key)
    {
        return \tap($this->get($key), function() use ($key) {
            $this->forget($key);
        });
    }

    /**
     * Removes the specified element from the collection, if it is found.
     *
     * @param mixed $element The element to remove.
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeElement($element): bool
    {
        if (($key = $this->indexOf($element)) !== false) {
            $this->remove($key);
            return true;
        }


        return false;
    }

    /**
     * Checks whether the collection contains an element with the specified key/index.
     *
     * @param string|int $key The key/index to check for.
     *
     * @return bool TRUE if the collection contains an element with the specified key/index,
     *              FALSE otherwise.
     */
    public function containsKey($key): bool
    {
        return $this->has($key);
    }

    /**
     * Gets all keys/indices of the collection.
     *
     * @return array The keys/indices of the collection, in the order of the corresponding
     *               elements in the collection.
     */
    public function getKeys(): array
    {
        return \array_keys($this->items);
    }

    /**
     * Gets all values of the collection.
     *
     * @return array The values of all elements in the collection, in the order they
     *               appear in the collection.
     */
    public function getValues(): array
    {
        return \array_values($this->items);
    }

    /**
     * Sets an element in the collection at the specified key/index.
     *
     * @param string|int $key   The key/index of the element to set.
     * @param mixed      $value The element to set.
     *
     * @return void
     */
    public function set($key, $value): void
    {
        $this->put($key, $value);
    }

    /**
     * Gets the key/index of the element at the current iterator position.
     *
     * @return int|string
     */
    public function key()
    {
        return \key($this->items);
    }

    /**
     * Gets the element of the collection at the current iterator position.
     *
     * @return mixed
     */
    public function current()
    {
        return \current($this->items);
    }

    /**
     * Moves the internal iterator position to the next element and returns this element.
     *
     * @return mixed
     */
    public function next()
    {
        return \next($this->items);
    }

    /**
     * Tests for the existence of an element that satisfies the given predicate.
     *
     * @param \Closure $filter The predicate.
     *
     * @return bool TRUE if the predicate is TRUE for at least one element, FALSE otherwise.
     */
    public function exists(\Closure $filter): bool
    {
        return $this->first($filter) !== null;
    }

    /**
     * Tests whether the given predicate p holds for all elements of this collection.
     *
     * @param \Closure $filter The predicate.
     *
     * @return bool TRUE, if the predicate yields TRUE for all elements, FALSE otherwise.
     */
    public function forAll(\Closure $filter): bool
    {
        return $this->every($filter);
    }

    /**
     * Gets the index/key of a given element. The comparison of two elements is strict,
     * that means not only the value but also the type must match.
     * For objects this means reference equality.
     *
     * @param mixed $element The element to search for.
     *
     * @return int|string|bool The key/index of the element or FALSE if the element was not found.
     */
    public function indexOf($element)
    {
        foreach($this->items as $key => $value) {
            if ($value === $element) {
                return $key;
            }
        }

        return false;
    }
}
