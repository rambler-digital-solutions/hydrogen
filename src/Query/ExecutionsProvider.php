<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Query;

use RDS\Hydrogen\Query;
use RDS\Hydrogen\Collection\Collection;

/**
 * Class ExecutionsProvider
 * @mixin Query
 */
trait ExecutionsProvider
{
    /**
     * @param string ...$fields
     * @return object[]|iterable
     */
    public function get(string ...$fields): iterable
    {
        $processor = $this->getRepository()->getProcessor();

        if (\count($fields) === 0) {
            return $processor->getResult($this);
        }

        return Collection::wrap($processor->getArrayResult($this))
            ->map(function ($item) use ($fields) {
                $result = [];

                foreach ($fields as $field) {
                    $result[$field] = \data_get($item, $field);
                }

                return $result;
            })
            ->toArray();
    }

    /**
     * @param string $field
     * @param string|null $typeOf
     * @return mixed
     * @throws \LogicException
     */
    public function scalar(string $field, string $typeOf = null)
    {
        $result = \data_get($this->first($field), $field);

        if ($typeOf !== null) {
            return $this->cast($result, $typeOf);
        }

        return $result;
    }

    /**
     * @param mixed $result
     * @param string $typeOf
     * @return array|\Closure|object
     */
    private function cast($result, string $typeOf)
    {
        $typeOf = \strtolower($typeOf);

        switch ($typeOf) {
            case 'callable':
                return function (callable $applicator = null) use ($result) {
                    return ($applicator ?? '\\value')($result);
                };

            case 'object':
                return (object)$result;

            case 'array':
            case 'iterable':
                return (array)$result;
        }

        $function = $typeOf . 'val';

        if (! \function_exists($function)) {
            throw new \InvalidArgumentException('Could not cast to type ' . $typeOf);
        }

        return $function($result);
    }

    /**
     * @param string|null $field
     * @return int
     * @throws \LogicException
     */
    public function count(?string $field = 'id'): int
    {
        return $this->select('COUNT(' . $field . ') AS __count')
            ->scalar('__count', 'int');
    }

    /**
     * @param string|null $field
     * @return int
     * @throws \LogicException
     */
    public function sum(string $field = null): int
    {
        return $this->select('SUM(' . $field . ') AS __sum')
            ->scalar('__sum', 'int');
    }

    /**
     * @param string|null $field
     * @return int
     * @throws \LogicException
     */
    public function avg(string $field = null): int
    {
        return $this->select('AVG(' . $field . ') AS __avg')
            ->scalar('__avg', 'int');
    }

    /**
     * @param string|null $field
     * @return int
     * @throws \LogicException
     */
    public function max(string $field = null): int
    {
        return $this->select('MAX(' . $field . ') AS __max')
            ->scalar('__max', 'int');
    }

    /**
     * @param string|null $field
     * @return int
     * @throws \LogicException
     */
    public function min(string $field = null): int
    {
        return $this->select('MIN(' . $field . ') AS __min')
            ->scalar('__min', 'int');
    }

    /**
     * @param string ...$fields
     * @return Collection
     */
    public function collect(string ...$fields): Collection
    {
        return Collection::wrap($this->get(...$fields));
    }

    /**
     * @param string[] $fields
     * @return object|null
     * @throws \LogicException
     */
    public function first(string ...$fields)
    {
        return \array_first($this->get(...$fields));
    }
}
