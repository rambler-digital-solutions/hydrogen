<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen;

use Doctrine\Common\Persistence\ObjectRepository;
use Illuminate\Support\Traits\Macroable;
use RDS\Hydrogen\Criteria\CriterionInterface;
use RDS\Hydrogen\Query\AliasProvider;
use RDS\Hydrogen\Query\GroupByProvider;
use RDS\Hydrogen\Query\LimitAndOffsetProvider;
use RDS\Hydrogen\Query\OrderProvider;
use RDS\Hydrogen\Query\RelationProvider;
use RDS\Hydrogen\Query\RepositoryProvider;
use RDS\Hydrogen\Query\ExecutionsProvider;
use RDS\Hydrogen\Query\SelectProvider;
use RDS\Hydrogen\Query\WhereProvider;

/**
 * Class Query
 */
class Query implements \IteratorAggregate
{
    use Macroable {
        Macroable::__call as __macroableCall;
    }
    use AliasProvider;
    use WhereProvider;
    use OrderProvider;
    use SelectProvider;
    use GroupByProvider;
    use RelationProvider;
    use RepositoryProvider;
    use ExecutionsProvider;
    use LimitAndOffsetProvider;

    /**
     * @var CriterionInterface[]|\SplObjectStorage
     */
    protected $criteria;

    /**
     * @var array|ObjectRepository[]
     */
    protected $scopes = [];

    /**
     * Query constructor.
     * @param ObjectRepository|null $repository
     */
    public function __construct(ObjectRepository $repository = null)
    {
        $this->criteria = new \SplObjectStorage();

        if ($repository) {
            $this->from($repository);
        }
    }

    /**
     * Adds the specified set of scopes (method groups) to the query.
     *
     * @param object|string ...$scopes
     * @return Query|$this
     */
    public function scope(...$scopes): self
    {
        $this->scopes = \array_merge($this->scopes, $scopes);

        return $this;
    }

    /**
     * Returns a list of selection criteria.
     *
     * @return \Generator|CriterionInterface[]
     */
    public function getCriteria(): \Generator
    {
        yield from $this->criteria;
    }

    /**
     * Creates a new query (alias to the constructor).
     *
     * @param CriterionInterface $criterion
     * @return Query|$this
     */
    public function add(CriterionInterface $criterion): self
    {
        $this->criteria->attach($criterion->withQuery($this));

        return $this;
    }

    /**
     * Creates a new query (alias to the constructor).
     *
     * @param ObjectRepository|null $repository
     * @return Query
     */
    public static function new(ObjectRepository $repository = null): Query
    {
        return new static($repository);
    }

    /**
     * @param string $name
     * @return null
     */
    public function __get(string $name)
    {
        if (\method_exists($this, $name)) {
            return $this->$name();
        }

        return null;
    }

    /**
     * @param string $method
     * @param array $parameters
     * @return mixed|$this|Query
     */
    public function __call(string $method, array $parameters = [])
    {
        foreach ($this->scopes as $scope) {
            if (\method_exists($scope, $method)) {
                /** @var Query $query */
                $query = \is_object($scope)
                    ? clone $scope->$method(...$parameters)
                    : clone $scope::$method(...$parameters);

                foreach ($query->getCriteria() as $criterion) {
                    $this->add($criterion);
                }

                return $this;
            }
        }

        return $this->__macroableCall($method, $parameters);
    }

    /**
     * Returns a set of scopes for the specified query.
     *
     * @return array|ObjectRepository[]
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    /**
     * Attaches a child query to the parent without affecting
     * the set of criteria (selections).
     *
     * @param Query $query
     * @return Query
     */
    public function attach(Query $query): Query
    {
        $this->repository
            ? $query->from($this->getRepository())
            : $query->alias = $this->getAlias();

        return $query;
    }

    /**
     * @param string $alias
     * @return Query|$this|self
     */
    public function withAlias(string $alias): Query
    {
        $this->alias = $alias;

        foreach ($this->criteria as $criterion) {
            $criterion->withAlias($alias);
        }

        return $this;
    }

    /**
     * Creates a new query using the current set of scopes.
     *
     * @return Query
     */
    public function create(): Query
    {
        return static::new()->scope(...$this->getScopes());
    }

    /**
     * Copies a set of Criteria from the child query to the parent.
     *
     * @param Query $query
     * @return Query
     */
    public function merge(Query $query): Query
    {
        foreach ($query->getCriteria() as $criterion) {
            $criterion->withAlias($query->getAlias());
            $this->add($criterion);
        }

        return $this;
    }

    /**
     * @return void
     */
    public function __clone()
    {
        $reflection = new \ReflectionClass($this);

        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($this);

            if (\is_object($value)) {
                $property->setValue($this, clone $value);
            }
        }
    }

    /**
     * @return \Generator
     */
    public function getIterator(): \Generator
    {
        foreach ($this->get() as $result) {
            yield $result;
        }
    }
}
