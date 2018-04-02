<?php

namespace Dgame\Graph\Trace;

/**
 * Class DefaultNodeStateTracer
 * @package Dgame\Graph\Trace
 */
final class DefaultNodeStateTracer implements NodeStateTracerInterface
{
    /**
     * @var NodeStateTrace[]
     */
    private $states = [];

    /**
     * @param NodeStateTrace $state
     */
    public function pushNodeState(NodeStateTrace $state): void
    {
        $this->states[] = $state;
    }

    /**
     * @return NodeStateTrace
     */
    public function popFrontNodeState(): NodeStateTrace
    {
        return array_shift($this->states);
    }

    /**
     * @return NodeStateTrace
     */
    public function popBackNodeState(): NodeStateTrace
    {
        return array_pop($this->states);
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return !empty($this->states);
    }

    /**
     * @param string $trace
     *
     * @return bool
     */
    public function containsTrace(string $trace): bool
    {
        return strpos($this->getTrace(), $trace) !== false;
    }

    /**
     * @return string
     */
    public function getTrace(): string
    {
        $trace = [];
        foreach ($this->states as $state) {
            $trace[] = (string) $state;
        }

        return implode('->', $trace);
    }
}