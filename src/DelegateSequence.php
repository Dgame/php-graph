<?php

namespace Dgame\Graph;

/**
 * Class DelegateSequence
 * @package Dgame\Graph
 */

use function Dgame\Ensurance\ensure;

/**
 * Class DelegateSequence
 * @package Dgame\Graph
 */
final class DelegateSequence
{
    /**
     * @var Delegate[]
     */
    private $delegates = [];

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->delegates);
    }

    /**
     * @param string   $name
     * @param callable $closure
     *
     * @return Delegate
     */
    public function willExecute(string $name, callable $closure): Delegate
    {
        $delegate          = new Delegate($name, $closure);
        $this->delegates[] = $delegate;

        return $delegate;
    }

    /**
     * @param string   $name
     * @param callable $closure
     *
     * @return Delegate
     */
    public function push(string $name, callable $closure): Delegate
    {
        return $this->insertAt(0, $closure, $name);
    }

    /**
     * @param int|null    $index
     * @param callable    $closure
     * @param string|null $name
     *
     * @return Delegate
     */
    private function insertAt(?int $index, callable $closure, string $name): Delegate
    {
        $delegate = new Delegate($name, $closure);

        if ($index === null || $index >= $this->count()) {
            $this->delegates[] = $delegate;
        } else {
            array_splice($this->delegates, $index, 0, [$delegate]);
        }

        return $delegate;
    }

    /**
     * @param string $name
     *
     * @return int|null
     */
    private function getIndexOf(string $name): ?int
    {
        foreach ($this->delegates as $index => $delegate) {
            if ($delegate->getName() === $name) {
                return $index;
            }
        }

        return null;
    }

    /**
     * @param string   $after
     * @param callable $closure
     * @param string   $name
     *
     * @return Delegate
     */
    public function willExecuteAfter(string $after, callable $closure, string $name): Delegate
    {
        $index = $this->getIndexOf($after);

        return $this->insertAt($index + 1, $closure, $name);
    }

    /**
     * @param string   $before
     * @param callable $closure
     * @param string   $name
     *
     * @return Delegate
     */
    public function willExecuteBefore(string $before, callable $closure, string $name): Delegate
    {
        $index = $this->getIndexOf($before);

        return $this->insertAt($index, $closure, $name);
    }

    /**
     * @param string       $name
     * @param Context|null $context
     *
     * @return bool
     */
    public function startWith(string $name, Context $context = null): bool
    {
        $context = $context ?? new Context();

        $index = $this->getIndexOf($name);
        ensure($index)->isNotNull()->orThrow('No such delegate: %s', $name);

        return $this->traverse(array_slice($this->delegates, $index), $context);
    }

    /**
     * @param Context $context
     *
     * @return bool
     */
    public function execute(Context $context = null): bool
    {
        $context = $context ?? new Context();

        return $this->traverse($this->delegates, $context);
    }

    /**
     * @param array   $delegates
     * @param Context $context
     *
     * @return bool
     */
    private function traverse(array $delegates, Context $context): bool
    {
        foreach ($delegates as $delegate) {
            if (!$this->run($delegate, $context)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param Delegate $delegate
     * @param Context           $context
     *
     * @return bool
     */
    private function run(Delegate $delegate, Context $context): bool
    {
        $closure = $delegate->getClosure();

        return $closure($context, $this) !== false;
    }
}
