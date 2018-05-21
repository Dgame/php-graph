<?php

namespace Dgame\Graph;

/**
 * Class DelegateSequence
 * @package Dgame\Graph
 */
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
     * @param callable    $closure
     * @param string|null $name
     *
     * @return Delegate
     */
    public function willExecute(callable $closure, string $name = null): Delegate
    {
        $delegate          = new Delegate($name ?? uniqid(), $closure);
        $this->delegates[] = $delegate;

        return $delegate;
    }

    /**
     * @param int|null    $index
     * @param callable    $closure
     * @param string|null $name
     *
     * @return Delegate
     */
    private function insertAt(?int $index, callable $closure, string $name = null): Delegate
    {
        $delegate = new Delegate($name ?? uniqid(), $closure);

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
     * @param string      $after
     * @param callable    $closure
     * @param string|null $name
     *
     * @return Delegate
     */
    public function willExecuteAfter(string $after, callable $closure, string $name = null): Delegate
    {
        $index = $this->getIndexOf($after);

        return $this->insertAt($index + 1, $closure, $name);
    }

    /**
     * @param string      $before
     * @param callable    $closure
     * @param string|null $name
     *
     * @return Delegate
     */
    public function willExecuteBefore(string $before, callable $closure, string $name = null): Delegate
    {
        $index = $this->getIndexOf($before);

        return $this->insertAt($index, $closure, $name);
    }

    /**
     *
     */
    public function execute(): void
    {
        foreach ($this->delegates as $delegate) {
            $closure = $delegate->getClosure();
            $closure();
        }
    }

    /**
     * @param Context $context
     */
    public function executeWith(Context $context): void
    {
        foreach ($this->delegates as $delegate) {
            $closure = $delegate->getClosure();
            $closure($context);
        }
    }
}
