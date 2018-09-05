<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Query;

use Doctrine\Common\Persistence\ObjectRepository;
use RDS\Hydrogen\Hydrogen;
use RDS\Hydrogen\Query;

/**
 * Trait RepositoryProvider
 * @mixin Query
 */
trait RepositoryProvider
{
    /**
     * @var ObjectRepository|Hydrogen
     */
    protected $repository;

    /**
     * @param ObjectRepository|Hydrogen $repository
     * @return Query|$this
     */
    public function from(ObjectRepository $repository): self
    {
        return $this->scope($this->repository = $repository);
    }

    /**
     * @return ObjectRepository|Hydrogen
     * @throws \LogicException
     */
    public function getRepository(): ObjectRepository
    {
        if ($this->repository === null) {
            $error = 'Query should be attached to repository';
            throw new \LogicException($error);
        }

        $this->bootIfNotBooted();

        return $this->repository;
    }
}
