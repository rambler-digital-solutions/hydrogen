<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Processor\DatabaseProcessor;

use Doctrine\ORM\Mapping\ClassMetadata;
use RDS\Hydrogen\Processor\BuilderInterface;
use RDS\Hydrogen\Processor\ProcessorInterface;
use RDS\Hydrogen\Query;

/**
 * Class Builder
 */
abstract class Builder implements BuilderInterface
{
    /**
     * @var ProcessorInterface
     */
    protected $processor;

    /**
     * @var Query
     */
    protected $query;

    /**
     * Builder constructor.
     * @param Query $query
     * @param ProcessorInterface $processor
     */
    public function __construct(Query $query, ProcessorInterface $processor)
    {
        $this->processor = $processor;
        $this->query = $query;
    }

    /**
     * @param string $entity
     * @param Query $query
     * @return iterable
     */
    protected function execute(string $entity, Query $query): iterable
    {
        return $this->processor->getProcessor($entity)->getResult($query);
    }

    /**
     * @return \Generator
     */
    protected function nothing(): \Generator
    {
        if (false) {
            yield;
        }
    }
}
