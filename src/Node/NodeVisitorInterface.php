<?php

namespace Dgame\Graph\Node;

/**
 * Interface NodeVisitorInterface
 * @package Dgame\Graph\Node
 */
interface NodeVisitorInterface
{
    /**
     * @param NodeInterface $node
     */
    public function visitNode(NodeInterface $node): void;

    /**
     * @param TransitionNodeInterface $node
     */
    public function visitTransitionNode(TransitionNodeInterface $node): void;

    /**
     * @param ProcessNodeInterface $node
     *
     * @return void
     */
    public function visitProcessNode(ProcessNodeInterface $node): void;
}