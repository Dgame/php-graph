<?php

namespace Dgame\Graph\Node;

/**
 * Class TransitionNode
 * @package Dgame\Graph\Node
 */
class TransitionNode extends Node implements TransitionNodeInterface
{
    /**
     * @var Transition[]
     */
    private $transitions = [];

    /**
     * @return bool
     */
    final public function hasTransitions(): bool
    {
        return !empty($this->transitions);
    }

    /**
     * @return Transition[]
     */
    final public function getTransitions(): array
    {
        return $this->transitions;
    }

    /**
     * @param NodeInterface $node
     * @param string|null   $description
     */
    final public function setTransitionTo(NodeInterface $node, string $description = null): void
    {
        $this->setTransition(new Transition($node, $description));
    }

    /**
     * @param NodeInterface   $node
     * @param int|null        $index
     *
     * @return bool
     */
    private function canFindIndexToTransition(NodeInterface $node, int &$index = null): bool
    {
        foreach ($this->transitions as $index => $transition) {
            if ($transition->isTransitionTo($node)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param NodeInterface $node
     *
     * @return Transition|null
     */
    final public function getTransitionTo(NodeInterface $node): ?Transition
    {
        return $this->canFindIndexToTransition($node, $index) ? $this->transitions[$index] : null;
    }

    /**
     * @param NodeInterface $node
     *
     * @return bool
     */
    final public function hasTransitionTo(NodeInterface $node): bool
    {
        return $this->getTransitionTo($node) !== null;
    }

    /**
     * @param NodeInterface $node
     *
     * @return bool
     */
    final public function removeTransitionTo(NodeInterface $node): bool
    {
        if ($this->canFindIndexToTransition($node, $index)) {
            unset($this->transitions[$index]);

            return true;
        }

        return false;
    }

    /**
     * @param Transition $transition
     */
    final public function setTransition(Transition $transition): void
    {
        $this->transitions[] = $transition;
    }

    /**
     * @param NodeVisitorInterface $visitor
     */
    public function accept(NodeVisitorInterface $visitor): void
    {
        $visitor->visitTransitionNode($this);
    }
}