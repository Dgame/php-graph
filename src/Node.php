<?php

namespace Dgame\Graph;

/**
 * Class Node
 * @package Dgame\Graph
 */
/**
 * Class Node
 * @package Dgame\Graph
 */
final class Node extends Delegate implements NodeInterface
{
    /**
     * @var array
     */
    private $transitions = [];

    /**
     * @param NodeInterface $node
     */
    public function setTransitionTo(NodeInterface $node): void
    {
        $this->transitions[] = $node;
    }

    /**
     * @return NodeInterface[]
     */
    public function getTransitions(): array
    {
        return $this->transitions;
    }

    /**
     * @return bool
     */
    public function hasTransitions(): bool
    {
        return !empty($this->transitions);
    }

    /**
     * @param Context $context
     */
    public function executeWith(Context $context): void
    {
        $closure = $this->getClosure();
        $closure($context);

        foreach ($this->transitions as $node) {
            $node->executeWith($context);
        }
    }
}
