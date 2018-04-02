<?php

namespace Dgame\Graph\Node;

use Dgame\Graph\Context;

/**
 * Class ProcessNodeVisitor
 * @package Dgame\Graph\Node
 */
final class ProcessNodeVisitor implements NodeVisitorInterface
{
    /**
     * @var Context
     */
    private $context;
    /**
     * @var bool
     */
    private $matched = false;

    /**
     * ProcessNodeVisitor constructor.
     *
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * @return bool
     */
    public function isMatched(): bool
    {
        return $this->matched;
    }

    /**
     * @param NodeInterface $node
     */
    public function visitNode(NodeInterface $node): void
    {
    }

    /**
     * @param TransitionNodeInterface $node
     */
    public function visitTransitionNode(TransitionNodeInterface $node): void
    {
    }

    /**
     * @param ProcessNodeInterface $node
     */
    public function visitProcessNode(ProcessNodeInterface $node): void
    {
        if ($node->isFulfilledBy($this->context)) {
            $this->matched = $node->process($this->context);
        }
    }
}