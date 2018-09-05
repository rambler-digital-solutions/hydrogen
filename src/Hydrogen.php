<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use RDS\Hydrogen\Processor\DatabaseProcessor;
use RDS\Hydrogen\Processor\ProcessorInterface;

/**
 * Trait Hydrogen
 * @property-read Query|$this $query
 */
trait Hydrogen
{
    /**
     * @var ProcessorInterface
     */
    private $processor;

    /**
     * @return ProcessorInterface
     */
    public function getProcessor(): ProcessorInterface
    {
        if ($this->processor === null) {
            $this->processor = new DatabaseProcessor($this, $this->getEntityManager());
        }

        return $this->processor;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return parent::getEntityManager();
    }

    /**
     * @return Query|$this
     * @throws \LogicException
     */
    public function query(): Query
    {
        if (! $this instanceof EntityRepository) {
            $error = 'Could not use %s under non-repository class, but %s given';
            throw new \LogicException(\sprintf($error, Hydrogen::class, static::class));
        }

        return Query::new()->from($this);
    }

    /**
     * @param string $name
     * @return null|Query
     * @throws \LogicException
     */
    public function __get(string $name)
    {
        switch ($name) {
            case 'query':
                return $this->query();
        }


    }
}
