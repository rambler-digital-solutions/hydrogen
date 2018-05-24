<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Collection;

use Illuminate\Support\Str;
use RDS\Hydrogen\Collection;

/**
 * Class HigherOrderCollectionProxy
 */
class HigherOrderCollectionProxy
{
    /**
     * This pattern is used to specify the location of the delegate in
     * the function arguments in the high-order messaging.
     */
    public const PATTERN = '_';

    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var string
     */
    private $method;

    /**
     * HighOrderCollectionProxy constructor.
     * @param Collection $collection
     * @param string $method
     */
    public function __construct(Collection $collection, string $method)
    {
        $this->collection = $collection;
        $this->method = $method;
    }

    /**
     * @param string $property
     * @return Collection|mixed
     */
    public function __get(string $property)
    {
        return $this->collection->{$this->method}(function($item) use ($property) {
            if ($this->hasProperty($item, $property)) {
                return $item->$property;
            }

            if ($this->isArrayable($item)) {
                return $item[$property];
            }

            if ($this->hasMethod($item, $property)) {
                return $item->$property();
            }

            if (\function_exists($property)) {
                return $property($item);
            }

            $snake = Str::snake($property);

            if (\function_exists($snake)) {
                return $snake($item);
            }

            return null;
        });
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return Collection|mixed
     */
    public function __call(string $method, array $arguments = [])
    {
        return $this->collection->{$this->method}(function($item) use ($method, $arguments) {
            if ($this->hasMethod($item, $method)) {
                return $item->$method(...$arguments);
            }

            if ($this->hasCallableProperty($item, $method)) {
                return ($item->$method)(...$arguments);
            }

            if ($this->hasCallableKey($item, $method)) {
                return $item[$method](...$arguments);
            }

            if (\function_exists($method)) {
                return $method(...$this->pack($item, $arguments));
            }

            $snake = Str::snake($method);
            if (\function_exists(Str::snake($snake))) {
                return $snake(...$this->pack($item, $arguments));
            }

            return null;
        });
    }

    /**
     * @param object $context
     * @return bool
     */
    private function isArrayable($context): bool
    {
        return \is_array($context) || $context instanceof \ArrayAccess;
    }

    /**
     * @param object $context
     * @param string $key
     * @return bool
     */
    private function hasCallableKey($context, string $key): bool
    {
        return $this->isArrayable($context) && \is_callable($context[$key] ?? null);
    }

    /**
     * @param object $context
     * @param string $property
     * @return bool
     */
    private function hasProperty($context, string $property): bool
    {
        return \is_object($context) && (
            \property_exists($context, $property) ||
            \method_exists($context, '__get')
        );
    }

    /**
     * @param object $context
     * @param string $property
     * @return bool
     */
    private function hasCallableProperty($context, string $property): bool
    {
        return $this->hasProperty($context, $property) && \is_callable($context->$property);
    }

    /**
     * @param object $context
     * @param string $method
     * @return bool
     */
    private function hasMethod($context, string $method): bool
    {
        return \is_object($context) && (
            \method_exists($context, $method) ||
            \method_exists($context, '__call')
        );
    }

    /**
     * @param object $context
     * @param array $parameters
     * @return array
     */
    private function pack($context, array $parameters): array
    {
        $result = [];

        foreach ($parameters as $parameter) {
            $result[] = $parameter === self::PATTERN ? $context : $parameter;
        }

        return $result;
    }
}
