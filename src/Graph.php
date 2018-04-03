<?php

namespace Dgame\Graph;

use Dgame\Graph\Node\TransitionNodeInterface;

/**
 * Class Graph
 * @package Dgame\Graph
 */
final class Graph
{
    /**
     * @var TransitionNodeInterface[]
     */
    private $nodes = [];

    /**
     * Graph constructor.
     *
     * @param array $nodes
     */
    public function __construct(array $nodes = [])
    {
        foreach ($nodes as $node) {
            $this->setNode($node);
        }
    }

    /**
     * @param TransitionNodeInterface $node
     */
    public function setNode(TransitionNodeInterface $node): void
    {
        $this->nodes[$node->getName()] = $node;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasNode(string $name): bool
    {
        return array_key_exists($name, $this->nodes);
    }

    /**
     * @param string $name
     *
     * @return TransitionNodeInterface
     */
    public function getNode(string $name): TransitionNodeInterface
    {
        return $this->nodes[$name];
    }

    /**
     * @param int $index
     *
     * @return TransitionNodeInterface
     */
    private function getNodeByIndex(int $index): TransitionNodeInterface
    {
        return array_values($this->nodes)[$index];
    }

    /**
     * @return TransitionNodeInterface[]
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }

    /**
     * @return TransitionNodeInterface
     */
    public function getFirstNode(): TransitionNodeInterface
    {
        return reset($this->nodes);
    }

    /**
     * @return TransitionNodeInterface
     */
    public function getLastNode(): TransitionNodeInterface
    {
        return end($this->nodes);
    }

    /**
     *
     */
    public function setForwardTransition(): void
    {
        for ($i = 0, $c = count($this->nodes) - 1; $i < $c; $i++) {
            $this->getNodeByIndex($i)->setTransitionTo($this->getNodeByIndex($i + 1));
        }
    }

    /**
     *
     */
    public function setBackwardTransition(): void
    {
        for ($i = count($this->nodes) - 1; $i > 0; $i--) {
            $this->getNodeByIndex($i)->setTransitionTo($this->getNodeByIndex($i - 1));
        }
    }

    /**
     *
     */
    public function setForwardCycleTransition(): void
    {
        $this->setForwardTransition();
        $this->getLastNode()->setTransitionTo($this->getFirstNode());
    }

    /**
     *
     */
    public function setBackwardCycleTransition(): void
    {
        $this->setBackwardTransition();
        $this->getFirstNode()->setTransitionTo($this->getLastNode());
    }
}