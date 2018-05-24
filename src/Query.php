<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen;

use Illuminate\Support\Traits\Macroable;
use RDS\Hydrogen\Criteria\Common\Operator;
use RDS\Hydrogen\Criteria\CriterionInterface;
use RDS\Hydrogen\Criteria\Group;
use RDS\Hydrogen\Criteria\GroupBy;
use RDS\Hydrogen\Criteria\Limit;
use RDS\Hydrogen\Criteria\Offset;
use RDS\Hydrogen\Criteria\OrderBy;
use RDS\Hydrogen\Criteria\Relation;
use RDS\Hydrogen\Criteria\Selection;
use RDS\Hydrogen\Criteria\Where;
use RDS\Hydrogen\Repository\ObjectRepository;
use RDS\Hydrogen\Repository\Selectable;

/**
 * @property-read Query $and
 * @property-read Query $or
 */
class Query
{
    use Macroable;

    /**
     * - AND if true
     * - OR if false
     *
     * @var bool
     */
    private $conjunction = true;

    /**
     * @var CriterionInterface[]|\SplObjectStorage
     */
    private $criteria;

    /**
     * @var array|Selectable[]
     */
    private $scopes = [];

    /**
     * @var ObjectRepository
     */
    private $repository;

    /**
     * Query constructor.
     */
    public function __construct()
    {
        $this->criteria = new \SplObjectStorage();
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic(string $method, array $arguments = [])
    {
        return static::new()->$method(...$arguments);
    }

    /**
     * @param array|string ...$fields
     * @return Query|$this|self
     */
    public function select(...$fields): self
    {
        foreach ($fields as $field) {
            \assert(\is_array($field) || \is_string($field));

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

    /**
     * @param CriterionInterface $criterion
     * @return Query|$this|self
     */
    public function add(CriterionInterface $criterion): self
    {
        $this->criteria->attach($criterion);

        return $this;
    }

    /**
     * @param string $field
     * @param mixed $from
     * @param mixed $to
     * @return Query|$this|self
     */
    public function orWhereBetween(string $field, $from, $to): self
    {
        return $this->or->whereBetween($field, $from, $to);
    }

    /**
     * @param string $field
     * @param mixed $from
     * @param mixed $to
     * @return Query|$this|self
     */
    public function whereBetween(string $field, $from, $to): self
    {
        return $this->add(new Where($field, Operator::BTW, [$from, $to], $this->mode()));
    }

    /**
     * @return bool
     */
    private function mode(): bool
    {
        return \tap($this->conjunction, function () {
            $this->conjunction = true;
        });
    }

    /**
     * @param string $field
     * @param mixed $from
     * @param mixed $to
     * @return Query|$this|self
     */
    public function orWhereNotBetween(string $field, $from, $to): self
    {
        return $this->or->whereBetween($field, $from, $to);
    }

    /**
     * @param string $field
     * @param mixed $from
     * @param mixed $to
     * @return Query|$this|self
     */
    public function whereNotBetween(string $field, $from, $to): self
    {
        return $this->add(new Where($field, Operator::NOT_BTW, [$from, $to], $this->mode()));
    }

    /**
     * @param string $field
     * @param iterable $value
     * @return Query|$this|self
     */
    public function orWhereIn(string $field, iterable $value): self
    {
        return $this->or->whereIn($field, $value);
    }

    /**
     * @param string $field
     * @param iterable|array $value
     * @return Query|$this|self
     */
    public function whereIn(string $field, iterable $value): self
    {
        return $this->add(new Where($field, Operator::IN, $value, $this->mode()));
    }

    /**
     * @param string $field
     * @param iterable $value
     * @return Query|$this|self
     */
    public function orWhereNotIn(string $field, iterable $value): self
    {
        return $this->or->whereNotIn($field, $value);
    }

    /**
     * @param string $field
     * @param iterable $value
     * @return Query|$this|self
     */
    public function whereNotIn(string $field, iterable $value): self
    {
        return $this->add(new Where($field, Operator::NOT_IN, $value, $this->mode()));
    }

    /**
     * @param string|\Closure $field
     * @param $valueOrOperator
     * @param null $value
     * @return Query|$this|self
     */
    public function orWhere($field, $valueOrOperator = null, $value = null): self
    {
        return $this->or->where($field, $valueOrOperator, $value);
    }

    /**
     * @param string|\Closure $field
     * @param $valueOrOperator
     * @param null $value
     * @return Query|$this|self
     */
    public function where($field, $valueOrOperator = null, $value = null): self
    {
        if (\is_string($field)) {
            [$operator, $value] = Where::completeMissingParameters($valueOrOperator, $value);

            return $this->add(new Where($field, $operator, $value, $this->mode()));
        }

        if ($field instanceof \Closure) {
            return $this->add(new Group($this, $field, $this->mode()));
        }

        $error = \vsprintf('Selection set should be a type of string or Closure, but %s given', [
            \studly_case(\gettype($field)),
        ]);

        throw new \InvalidArgumentException($error);
    }

    /**
     * @param string $field
     * @return Query|$this|self
     */
    public function orWhereNull(string $field): self
    {
        return $this->or->whereNull($field);
    }

    /**
     * @param string $field
     * @return Query|$this|self
     */
    public function whereNull(string $field): self
    {
        return $this->add(new Where($field, Operator::EQ, null, $this->mode()));
    }

    /**
     * @param string $field
     * @return Query|$this|self
     */
    public function orWhereNotNull(string $field): self
    {
        return $this->or->whereNotNull($field);
    }

    /**
     * @param string $field
     * @return Query|$this|self
     */
    public function whereNotNull(string $field): self
    {
        return $this->add(new Where($field, Operator::NEQ, null, $this->mode()));
    }

    /**
     * An alias of "limit(...)"
     *
     * @param int $count
     * @return Query|$this|self
     */
    public function take(int $count): self
    {
        return $this->limit($count);
    }

    /**
     * @param int $count
     * @return Query|$this|self
     */
    public function limit(int $count): self
    {
        return $this->add(new Limit($count));
    }

    /**
     * An alias of "offset(...)"
     *
     * @param int $count
     * @return Query|$this|self
     */
    public function skip(int $count): self
    {
        return $this->offset($count);
    }

    /**
     * @param int $count
     * @return Query|$this|self
     */
    public function offset(int $count): self
    {
        return $this->add(new Offset($count));
    }

    /**
     * @param int $from
     * @param int $to
     * @return Query|$this|self
     */
    public function range(int $from, int $to): self
    {
        if ($from > $to) {
            throw new \InvalidArgumentException('From value must be less than To');
        }

        return $this->limit($from)->offset($to - $from);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return Query
     */
    public function after(string $field, $value): self
    {
        return $this->where($field, '>', $value)->asc($field);
    }

    /**
     * @param string ...$fields
     * @return Query|$this|self
     */
    public function asc(string ...$fields): self
    {
        foreach ($fields as $field) {
            $this->orderBy($field);
        }

        return $this;
    }

    /**
     * @param string $field
     * @param bool $asc
     * @return Query|$this|self
     */
    public function orderBy(string $field, bool $asc = true): self
    {
        return $this->add(new OrderBy($field, $asc));
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return Query
     */
    public function before(string $field, $value): self
    {
        return $this->where($field, '<', $value)->desc($field);
    }

    /**
     * @param string ...$fields
     * @return Query|$this|self
     */
    public function desc(string ...$fields): self
    {
        foreach ($fields as $field) {
            $this->orderBy($field);
        }

        return $this;
    }

    /**
     * @param string $field
     * @return Query|$this|self
     */
    public function latest(string $field = 'createdAt'): self
    {
        return $this->desc($field);
    }

    /**
     * @param string $field
     * @return Query|$this|self
     */
    public function oldest(string $field = 'createdAt'): self
    {
        return $this->asc($field);
    }

    /**
     * @param string[] $fields
     * @return Query|$this|self
     */
    public function groupBy(string ...$fields): self
    {
        foreach ($fields as $field) {
            $this->add(new GroupBy($field));
        }

        return $this;
    }

    /**
     * @param string[]|array[] $relations
     * @return Query|$this|self
     */
    public function with(...$relations): self
    {
        foreach ($relations as $relation) {
            if (\is_string($relation)) {
                $this->add(new Relation($relation, $this));
                continue;
            }

            if (\is_array($relation)) {
                foreach ($relation as $rel => $sub) {
                    \assert(\is_string($rel) && $sub instanceof \Closure);
                    $this->add(new Relation($rel, $this, $sub));
                }
                continue;
            }

            throw new \InvalidArgumentException('Invalid relation format');
        }

        return $this;
    }

    /**
     * @return Query|$this|self
     */
    public function and(): self
    {
        $this->conjunction = true;

        return $this;
    }

    /**
     * @return Query|$this|self
     */
    public function or(): self
    {
        $this->conjunction = false;

        return $this;
    }

    /**
     * @return Collection|iterable|object[]
     */
    public function collect(): Collection
    {
        return Collection::wrap($this->get());
    }

    /**
     * @return iterable|object[]
     */
    public function get(): iterable
    {
        $result = $this->repository()->findBy($this);

        if ($result instanceof \Traversable) {
            $result = \iterator_to_array($result);
        }

        return $result;
    }

    /**
     * @return ObjectRepository
     */
    protected function repository(): ObjectRepository
    {
        if ($this->repository === null) {
            throw new \LogicException('Can not exec query on undefined data source');
        }

        return $this->repository;
    }

    /**
     * @return null|object|mixed
     */
    public function first()
    {
        return $this->repository()->findOneBy($this);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->repository()->count($this);
    }

    /**
     * @param string $method
     * @return mixed
     */
    public function __get(string $method)
    {
        return $this->$method();
    }

    /**
     * @param Query $query
     * @return Query|$this|self
     */
    public function mergeWith(Query $query): Query
    {
        foreach ($query->getCriteria() as $criterion) {
            $this->add($criterion);
        }

        return $this;
    }

    /**
     * @return \Traversable|CriterionInterface[]
     */
    public function getCriteria(): iterable
    {
        yield from $this->criteria;
    }

    /**
     * @return Query
     */
    public function sub(): Query
    {
        return static::new()
            ->from($this->getFrom())
            ->scope(...\iterator_to_array($this->getScopes()));
    }

    /**
     * @param Selectable[] $contexts
     * @return Query|$this|self
     */
    public function scope(Selectable ...$contexts): self
    {
        foreach ($contexts as $context) {
            $this->scopes[] = $context;
        }

        return $this;
    }

    /**
     * @param ObjectRepository $repository
     * @return Query|$this|self|$repository
     */
    public function from(ObjectRepository $repository): self
    {
        return $this->scope($this->repository = $repository);
    }

    /**
     * @return self
     */
    public static function new(): self
    {
        return new static();
    }

    /**
     * @return ObjectRepository
     */
    public function getFrom(): ObjectRepository
    {
        return $this->repository();
    }

    /**
     * @return \Traversable|Selectable[]
     */
    public function getScopes(): \Traversable
    {
        yield from $this->scopes;
    }

    /**
     * @internal This method should only be used to optimize queries inside heuristic algorithms.
     *
     * @param CriterionInterface $original
     * @param CriterionInterface $new
     * @return void
     */
    public function replaceCriterion(CriterionInterface $original, CriterionInterface $new): void
    {
        $this->criteria->detach($original);
        $this->criteria->attach($new);
    }

    /**
     * @internal This method should only be used to optimize queries inside heuristic algorithms.
     *
     * @param CriterionInterface[] $criteria
     * @return void
     */
    public function removeCriterion(CriterionInterface ...$criteria): void
    {
        foreach ($criteria as $criterion) {
            $this->criteria->detach($criterion);
        }
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $method, array $arguments = [])
    {
        foreach ($this->scopes as $scope) {
            if (\method_exists($scope, $method)) {
                return $scope->scope($this)->$method(...$arguments);
            }
        }

        $scopes = \implode(', ', \array_map('\\class_basename', $this->scopes));
        $error  = \vsprintf('Can not find method %s in %s%s', [
            $method,
            $scopes ? $scopes . ' or ' : '',
            \class_basename($this),
        ]);

        throw new \BadMethodCallException($error);
    }
}
