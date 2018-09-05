<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection as BaseCollection;

/**
 * Class Collection
 * @mixin BaseCollection
 */
class Collection extends ArrayCollection
{
    /**
     * @var array|string[]
     */
    private static $proxies;

    /**
     * @var BaseCollection
     */
    private $inner;

    /**
     * Collection constructor.
     * @param array|iterable $elements
     */
    public function __construct($elements = [])
    {
        $this->inner = BaseCollection::wrap($elements);

        parent::__construct($this->inner->toArray());

        $this->exportProxies();
    }

    /**
     * @return void
     */
    private function exportProxies(): void
    {
        if (static::$proxies === null) {
            $class    = new \ReflectionClass($this->inner);
            $property = $class->getProperty('proxies');
            $property->setAccessible(true);

            static::$proxies = $property->getValue();
        }
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws \BadMethodCallException
     */
    public static function __callStatic(string $name, array $arguments = [])
    {
        if (\method_exists(BaseCollection::class, $name)) {
            $result = BaseCollection::$name(...$arguments);

            if ($result instanceof BaseCollection) {
                return new static($result->toArray());
            }

            return $result;
        }

        $error = \sprintf('Call to undefined method %s::%s', static::class, $name);
        throw new \BadMethodCallException($error);
    }

    /**
     * Wrap the given value in a collection if applicable.
     *
     * @param mixed $value
     * @return static
     */
    public static function wrap($value): self
    {
        switch (true) {
            case $value instanceof self:
                return new static($value);

            case $value instanceof BaseCollection:
                return new static($value);

            default:
                return new static(Arr::wrap($value));
        }
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call(string $name, array $arguments = [])
    {
        if (\method_exists($this->inner, $name)) {
            $result = $this->inner->$name(...$arguments);

            if ($result instanceof BaseCollection) {
                return new static($result->toArray());
            }

            return $result;
        }

        $error = \sprintf('Call to undefined method %s::%s', static::class, $name);
        throw new \BadMethodCallException($error);
    }

    /**
     * @param string $key
     * @return HigherOrderCollectionProxy
     * @throws \InvalidArgumentException
     */
    public function __get(string $key): HigherOrderCollectionProxy
    {
        if (! \in_array($key, static::$proxies, true)) {
            $error = "Property [{$key}] does not exist on this collection instance.";
            throw new \InvalidArgumentException($error);
        }

        return new HigherOrderCollectionProxy($this, $key);
    }
}
