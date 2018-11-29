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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Illuminate\Support\Traits\Macroable;
use RDS\Hydrogen\Criteria\CriterionInterface;
use RDS\Hydrogen\Query\AliasProvider;
use RDS\Hydrogen\Query\ExecutionsProvider;
use RDS\Hydrogen\Query\GroupByProvider;
use RDS\Hydrogen\Query\LimitAndOffsetProvider;
use RDS\Hydrogen\Query\ModeProvider;
use RDS\Hydrogen\Query\OrderProvider;
use RDS\Hydrogen\Query\RelationProvider;
use RDS\Hydrogen\Query\RepositoryProvider;
use RDS\Hydrogen\Query\SelectProvider;
use RDS\Hydrogen\Query\WhereProvider;

/**
 * A base class for all queries, contains the execution context
 * and a set of methods for adding criteria to this context.
 *
 * To add new methods during runtime, you can use the
 * `Query::macro(..)` method.
 */
class Query implements \IteratorAggregate
{
    use Macroable {
        Macroable::__call as __macroableCall;
        Macroable::__callStatic as __macroableCallStatic;
    }

    use ModeProvider;
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
     * Contains the status of the download. Before any request,
     * you need to make sure that all the runtime is loaded.
     *
     * It is this perennial one that indicates if at least one
     * query has already been created in order to load the
     * necessary functions.
     *
     * @var bool
     */
    private static $booted = false;

    /**
     * A set of query criteria in a given execution context.
     *
     * @var CriterionInterface[]
     */
    protected $criteria = [];

    /**
     * A set of scopes (classes and objects) that have access to be
     * able to create a query from a set of methods defined
     * in the specified scopes.
     *
     * @var array|ObjectRepository[]
     */
    protected $scopes = [];

    /**
     * @param ObjectRepository|null $repository
     */
    public function __construct(ObjectRepository $repository = null)
    {
        if ($repository !== null) {
            $this->from($repository);
        }
    }

    /**
     * Method for creating native DB queries or query parts.
     *
     * @param string $stmt
     * @return string
     */
    public static function raw(string $stmt): string
    {
        return \sprintf("RAW('%s')", \addcslashes($stmt, "'"));
    }

    /**
     * The method checks for the presence of the required criterion inside the query.
     *
     * TODO Add callable argument support (like filter).
     *
     * @param string $criterion
     * @return bool
     */
    public function has(string $criterion): bool
    {
        foreach ($this->criteria as $haystack) {
            if (\get_class($haystack) === $criterion) {
                return true;
            }
        }

        return false;
    }

    /**
     * Provides the ability to directly access methods without specifying parentheses.
     *
     * TODO 1) Add High Order Messaging for methods like `->field->where(23)` instead `->where('field', 23)`
     * TODO 2) Allow inner access `->embedded->field->where(23)` instead `->where('embedded.field', 23)`
     *
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
     * Creates the ability to directly access the table's column.
     *
     * @param string $name
     * @return string
     */
    public function column(string $name): string
    {
        $name = \addcslashes($name, "'");
        $table = $this->getMetadata()->getTableName();

        return \sprintf("FIELD('%s', '%s', '%s')", $table, $this->getAlias(), $name);
    }

    /**
     * @internal For internal use only
     * @return ClassMetadata
     */
    public function getMetadata(): ClassMetadata
    {
        return $this->getEntityManager()->getClassMetadata($this->getClassName());
    }

    /**
     * @internal For internal use only
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->getRepository()->getEntityManager();
    }

    /**
     * @internal For internal use only
     * @return string
     */
    public function getClassName(): string
    {
        return $this->getRepository()->getClassName();
    }

    /**
     * @param string $method
     * @param array $parameters
     * @return mixed|$this|Query
     */
    public function __call(string $method, array $parameters)
    {
        if ($result = $this->callScopes($method, $parameters)) {
            return $result;
        }

        return $this->__macroableCall($method, $parameters);
    }

    /**
     * @param string $method
     * @param array $parameters
     * @return null|Query|mixed
     */
    private function callScopes(string $method, array $parameters = [])
    {
        foreach ($this->scopes as $scope) {
            if (\method_exists($scope, $method)) {
                /** @var Query $query */
                $query = \is_object($scope) ? $scope->$method(...$parameters) : $scope::$method(...$parameters);

                if ($query instanceof self) {
                    return $this->merge($query->clone());
                }

                return $query;
            }
        }

        return null;
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
            $criterion->attach($this);
        }

        return $this->attach($query);
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
     * @param Query $query
     * @return Query
     */
    public function attach(Query $query): Query
    {
        foreach ($query->getCriteria() as $criterion) {
            $this->add($criterion);
        }

        return $this;
    }

    /**
     * Creates a new query (alias to the constructor).
     *
     * @param CriterionInterface $criterion
     * @return Query|$this
     */
    public function add(CriterionInterface $criterion): self
    {
        if (! $criterion->isAttached()) {
            $criterion->attach($this);
        }

        $this->criteria[] = $criterion;

        return $this;
    }

    /**
     * @return Query
     */
    public function clone(): Query
    {
        $clone = $this->create();

        foreach ($this->criteria as $criterion) {
            $criterion = clone $criterion;

            if ($criterion->isAttachedTo($this)) {
                $criterion->attach($clone);
            }

            $clone->add($criterion);
        }

        return $clone;
    }

    /**
     * Creates a new query using the current set of scopes.
     *
     * @return Query
     */
    public function create(): Query
    {
        $query = static::new()->scope(...$this->getScopes());

        if ($this->repository) {
            return $query->from($this->repository);
        }

        return $query;
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
     * Returns a set of scopes for the specified query.
     *
     * @return array|ObjectRepository[]
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    /**
     * @return void
     * @throws \LogicException
     */
    public function __clone()
    {
        $error = '%s not allowed. Use %s::clone() instead';

        throw new \LogicException(\sprintf($error, __METHOD__, __CLASS__));
    }

    /**
     * @param string|\Closure $filter
     * @return Query
     */
    public function except($filter): Query
    {
        if (\is_string($filter) && ! \is_callable($filter)) {
            return $this->only(function (CriterionInterface $criterion) use ($filter): bool {
                return ! $criterion instanceof $filter;
            });
        }

        return $this->only(function (CriterionInterface $criterion) use ($filter): bool {
            return ! $filter($criterion);
        });
    }

    /**
     * @param string|\Closure $filter
     * @return Query
     */
    public function only($filter): Query
    {
        $filter = $this->createFilter($filter);
        $copy = $this->clone();
        $criteria = [];

        foreach ($copy->getCriteria() as $criterion) {
            if ($filter($criterion)) {
                $criteria[] = $criterion;
            }
        }

        $copy->criteria = $criteria;

        return $copy;
    }

    /**
     * @param string|callable $filter
     * @return callable
     */
    private function createFilter($filter): callable
    {
        \assert(\is_string($filter) || \is_callable($filter));

        if (\is_string($filter) && ! \is_callable($filter)) {
            $typeOf = $filter;

            return function (CriterionInterface $criterion) use ($typeOf): bool {
                return $criterion instanceof $typeOf;
            };
        }

        return $filter;
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

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return \count($this->criteria) === 0;
    }

    /**
     * @return string
     */
    public function dump(): string
    {
        return $this->getRepository()->getProcessor()->dump($this);
    }

    /**
     * @return void
     */
    private function bootIfNotBooted(): void
    {
        if (self::$booted === false) {
            self::$booted = true;

            $bootstrap = new Bootstrap();
            $bootstrap->register($this->getRepository()->getEntityManager());
        }
    }
}
