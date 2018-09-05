<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Processor;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use RDS\Hydrogen\Query;

/**
 * Interface ProcessorInterface
 */
interface ProcessorInterface
{
    public const ENTITY_SECTION = 0x00;
    public const ADDITIONAL_FIELDS_SECTION = 0x01;

    /**
     * @param Query $query
     * @param string $field
     * @return mixed
     */
    public function getScalarResult(Query $query, string $field);

    /**
     * @param Query $query
     * @param string[] $fields
     * @return iterable
     */
    public function getResult(Query $query, string ...$fields): iterable;

    /**
     * @param Query $query
     * @return string
     */
    public function dump(Query $query): string;

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface;

    /**
     * @return ClassMetadata
     */
    public function getMetadata(): ClassMetadata;

    /**
     * @return ObjectRepository
     */
    public function getRepository(): ObjectRepository;

    /**
     * @param string $entity
     * @return ProcessorInterface
     */
    public function getProcessor(string $entity): ProcessorInterface;
}
