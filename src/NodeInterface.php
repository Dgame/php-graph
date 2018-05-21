<?php

namespace Dgame\Graph;

/**
 * Interface NodeInterface
 * @package Dgame\Graph
 */
interface NodeInterface extends DelegateInterface
{
    /**
     * @param NodeInterface $node
     */
    public function setTransitionTo(self $node): void;

    /**
     * @return NodeInterface[]
     */
    public function getTransitions(): array;

    /**
     * @return bool
     */
    public function hasTransitions(): bool;

    /**
     * @param Context $context
     */
    public function executeWith(Context $context): void;
}
