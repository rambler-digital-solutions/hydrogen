<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Processor;

/**
 * Class DeferredStack
 */
class Queue implements \IteratorAggregate
{
    /**
     * @var \SplQueue
     */
    private $queue;

    /**
     * Queue constructor.
     */
    public function __construct()
    {
        $this->queue = new \SplQueue();
    }

    /**
     * @param \Closure $deferred
     * @return Queue
     */
    public function push(\Closure $deferred): Queue
    {
        $this->queue->push($deferred);

        return $this;
    }

    /**
     * @return \Generator|\Closure[]
     */
    public function getIterator(): \Generator
    {
        while ($this->queue->count()) {
            yield $this->queue->pop();
        }
    }

    /**
     * @param array $args
     * @return \Generator
     */
    public function reduce(...$args): \Generator
    {
        foreach ($this->getIterator() as $item) {
            $output = $item(...$args);

            if ($output instanceof \Traversable) {
                yield $output;
            }
        }
    }
}
