<?php

namespace Dgame\Graph\Node;

/**
 * Interface TransitionNodeInterface
 * @package Dgame\Graph\Node
 */
interface TransitionNodeInterface extends NodeInterface
{
    /**
     * @return bool
     */
    public function hasTransitions(): bool;

    /**
     * @return Transition[]
     */
    public function getTransitions(): array;

    /**
     * @param NodeInterface $node
     * @param string|null   $description
     */
    public function setTransitionTo(NodeInterface $node, string $description = null): void;

    /**
     * @param NodeInterface $node
     *
     * @return bool
     */
    public function hasTransitionTo(NodeInterface $node): bool;

    /**
     * @param NodeInterface $node
     *
     * @return Transition|null
     */
    public function getTransitionTo(NodeInterface $node): ?Transition;

    /**
     * @param NodeInterface $node
     *
     * @return bool
     */
    public function removeTransitionTo(NodeInterface $node): bool;

    /**
     * @param Transition $transition
     */
    public function setTransition(Transition $transition): void;
}