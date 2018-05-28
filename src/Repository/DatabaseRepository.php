<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Repository;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use RDS\Hydrogen\Collection;
use RDS\Hydrogen\Processor\DatabaseProcessor;
use RDS\Hydrogen\Processor\ProcessorInterface;
use RDS\Hydrogen\Query;

/**
 * Class DatabaseRepository
 */
abstract class DatabaseRepository extends Repository
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ClassMetadata
     */
    private $meta;

    /**
     * DatabaseRepository constructor.
     * @param EntityManagerInterface $em
     * @param ClassMetadata $meta
     */
    public function __construct(EntityManagerInterface $em, ClassMetadata $meta)
    {
        $this->em   = $em;
        $this->meta = $meta;
    }

    /**
     * @param int|string $id
     * @return mixed|null|object
     */
    public function find($id)
    {
        $primary = \array_first($this->meta->getIdentifierFieldNames());

        return $this->findOneBy($this->query()->where($primary, $id));
    }

    /**
     * @param Query $query
     * @return null|object
     */
    public function findOneBy(Query $query)
    {
        return $this->getProcessor()->first($query);
    }

    /**
     * @return ProcessorInterface|DatabaseProcessor
     */
    public function getProcessor(): ProcessorInterface
    {
        return new DatabaseProcessor($this->em, $this->meta);
    }

    /**
     * @return iterable|object[]
     */
    public function findAll(): iterable
    {
        return $this->findBy($this->query());
    }

    /**
     * @param Query $query
     * @return Collection
     */
    public function findBy(Query $query): iterable
    {
        return $this->getProcessor()->get($query);
    }

    /**
     * @param Query $query
     * @return int
     */
    public function count(Query $query): int
    {
        return $this->getProcessor()->count($query);
    }

    /**
     * @param string $alias
     * @param null $indexBy
     * @return QueryBuilder
     */
    public function createQueryBuilder(string $alias, $indexBy = null): QueryBuilder
    {
        return $this->em->createQueryBuilder()
            ->select($alias)
            ->from($this->getClassName(), $alias, $indexBy);
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->meta->getName();
    }

    /**
     * @param Query $query
     * @return QueryBuilder
     */
    public function getQueryBuilder(Query $query): QueryBuilder
    {
        return $this->getProcessor()->toBuilder($query);
    }

    /**
     * @param Query $query
     * @return string
     */
    public function getDql(Query $query): string
    {
        return $this->getProcessor()->toBuilder($query)->getDQL();
    }

    /**
     * @param Query $query
     * @return mixed
     */
    public function getSql(Query $query)
    {
        return $this->getProcessor()->toBuilder($query)->getQuery()->getSQL();
    }
}
