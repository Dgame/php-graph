<?php

namespace Dgame\Graph\Trace;

use Dgame\Graph\Context;
use Dgame\Graph\Node\NodeInterface;

/**
 * Class NodeStateTrace
 * @package Dgame\Graph\Trace
 */
final class NodeStateTrace
{
    /**
     * @var NodeInterface
     */
    private $node;
    /**
     * @var bool
     */
    private $result = true;
    /**
     * @var Context
     */
    private $context;

    /**
     * NodeState constructor.
     *
     * @param NodeInterface $node
     * @param bool          $result
     * @param Context       $context
     */
    public function __construct(NodeInterface $node, bool $result, Context $context)
    {
        $this->node    = $node;
        $this->result  = $result;
        $this->context = clone $context;
    }

    /**
     * @return NodeInterface
     */
    public function getNode(): NodeInterface
    {
        return $this->node;
    }

    /**
     * @return bool
     */
    public function isResult(): bool
    {
        return $this->result;
    }

    /**
     * @return Context
     */
    public function getContext(): Context
    {
        return $this->context;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('%s(%d)', $this->node->getName(), $this->result);
    }
}