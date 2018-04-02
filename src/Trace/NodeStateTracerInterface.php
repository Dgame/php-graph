<?php

namespace Dgame\Graph\Trace;

/**
 * Interface NodeStateTracerInterface
 * @package Dgame\Graph
 */
interface NodeStateTracerInterface
{
    /**
     * @param NodeStateTrace $state
     */
    public function pushNodeState(NodeStateTrace $state): void;

    /**
     * @return NodeStateTrace
     */
    public function popFrontNodeState(): NodeStateTrace;

    /**
     * @return NodeStateTrace
     */
    public function popBackNodeState(): NodeStateTrace;

    /**
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * @param string $trace
     *
     * @return bool
     */
    public function containsTrace(string $trace): bool;

    /**
     * @return string
     */
    public function getTrace(): string;
}