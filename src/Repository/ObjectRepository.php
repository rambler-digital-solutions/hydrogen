<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Repository;

use RDS\Hydrogen\Collection;
use RDS\Hydrogen\Query;

/**
 * Interface ObjectRepository
 */
interface ObjectRepository extends Selectable
{
    /**
     * Finds an object by its primary key / identifier.
     *
     * @param int|string $id
     * @return object|null
     */
    public function find($id);

    /**
     * Finds all objects in the repository.
     *
     * @return iterable|object[]
     */
    public function findAll(): iterable;

    /**
     * Finds a single object by a set of criteria.
     *
     * @param Query $query
     * @return null|object
     */
    public function findOneBy(Query $query);

    /**
     * Finds objects by a set of criteria.
     *
     * Optionally sorting and limiting details can be passed. An implementation may throw
     * an UnexpectedValueException if certain values of the sorting or limiting details are
     * not supported.
     *
     * @param Query $query
     * @return iterable|object[]
     */
    public function findBy(Query $query): Collection;

    /**
     * @param Query $query
     * @return int
     */
    public function count(Query $query): int;

    /**
     * @return string
     */
    public function getClassName(): string;
}
