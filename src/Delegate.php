<?php

namespace Dgame\Graph;

use Closure;

/**
 * Class Delegate
 * @package Dgame\Graph
 */
class Delegate implements DelegateInterface
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var Closure
     */
    private $closure;

    /**
     * Delegate constructor.
     *
     * @param string  $name
     * @param Closure $closure
     */
    public function __construct(string $name, Closure $closure)
    {
        $this->name    = $name;
        $this->closure = $closure->bindTo($this);
    }

    /**
     * @return callable
     */
    final public function getClosure(): callable
    {
        return $this->closure;
    }

    /**
     * @return string
     */
    final public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param callable $condition
     */
    final public function if(callable $condition): void
    {
        $closure       = $this->closure;
        $this->closure = function (Context $context) use ($closure, $condition) {
            if (!!$condition($context)) {
                $closure($context);
            }
        };
    }

    /**
     * @param callable $condition
     */
    final public function ifNot(callable $condition): void
    {
        $closure       = $this->closure;
        $this->closure = function (Context $context) use ($closure, $condition) {
            if (!$condition($context)) {
                $closure($context);
            }
        };
    }

    /**
     * @param callable $condition
     */
    final public function while(callable $condition): void
    {
        $closure       = $this->closure;
        $this->closure = function (Context $context) use ($closure, $condition) {
            while (!!$condition($context)) {
                $closure($context);
            }
        };
    }

    /**
     * @param callable $condition
     */
    final public function until(callable $condition): void
    {
        $closure       = $this->closure;
        $this->closure = function (Context $context) use ($closure, $condition) {
            while (!$condition($context)) {
                $closure($context);
            }
        };
    }
}
