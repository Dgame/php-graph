<?php

namespace Dgame\Graph\Visualizer;

use Dgame\Graph\Graph;
use Dgame\Graph\Node\NodeInterface;
use Dgame\Graph\Node\NodeVisitorInterface;
use Dgame\Graph\Node\ProcessNodeInterface;
use Dgame\Graph\Node\TransitionNodeInterface;

/**
 * Class MermaidVisualizer
 * @package Dgame\Graph\Visualizer
 */
final class MermaidVisualizer implements VisualizerInterface, NodeVisitorInterface
{
    /**
     * @var int
     */
    private $letter = 1;
    /**
     * @var int
     */
    private $index = 1;
    /**
     * @var array
     */
    private $mermaid = [];
    /**
     * @var array
     */
    private $alias = [];
    /**
     * @var array
     */
    private $nodes = [];

    /**
     * MermaidGraphVisualizer constructor.
     *
     * @param Graph $graph
     */
    public function __construct(Graph $graph)
    {
        foreach ($graph->getNodes() as $node) {
            $node->accept($this);
        }
    }

    /**
     * @return array
     */
    public function getVisualized(): array
    {
        return $this->mermaid;
    }

    /**
     * @param NodeInterface $node
     *
     * @return string
     */
    private function getMermaidNodeOf(NodeInterface $node): string
    {
        $name = $node->getName();
        if ($this->hasAlias($name)) {
            return $this->getAliasOf($node);
        }

        $alias    = $this->getAliasOf($node);
        $template = new MermaidTemplate($node);

        $mermaidNode = sprintf($template->getTemplate(), $alias, $this->getMermaidNodeContentOf($node));
        $this->nodes[$name] = $mermaidNode;

        return $mermaidNode;
    }

    /**
     * @param NodeInterface $node
     *
     * @return string
     */
    private function getMermaidNodeContentOf(NodeInterface $node): string
    {
        return $node->hasDescription() ? $node->getDescription() : $node->getName();
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    private function hasAlias(string $name): bool
    {
        return array_key_exists($name, $this->alias);
    }

    /**
     * @param NodeInterface $node
     *
     * @return string
     */
    private function getAliasOf(NodeInterface $node): string
    {
        $name = $node->getName();
        if (!$this->hasAlias($name)) {
            $this->alias[$name] = $this->getNextAlias();
        }

        return $this->alias[$name];
    }

    /**
     * @return string
     */
    private function getNextAlias(): string
    {
        if ($this->letter > 26) {
            $this->letter = 1;
            $this->index++;
        }

        $letter = chr($this->letter + 64);
        $this->letter++;

        return sprintf('%s%d', $letter, $this->index);
    }

    /**
     * @param NodeInterface $node
     */
    public function visitNode(NodeInterface $node): void
    {
        $alias    = $this->getAliasOf($node);
        $template = new MermaidTemplate($node);

        $this->mermaid[] = sprintf($template->getTemplate(), $alias, $this->getMermaidNodeContentOf($node));
    }

    /**
     * @param TransitionNodeInterface $node
     */
    public function visitTransitionNode(TransitionNodeInterface $node): void
    {
        $sourceNode = $this->getMermaidNodeOf($node);
        foreach ($node->getTransitions() as $transition) {
            $targetNode = $this->getMermaidNodeOf($transition->getNode());
            if ($transition->hasDescription()) {
                $this->mermaid[] = sprintf('%s-->|%s| %s', $sourceNode, $transition->getDescription(), $targetNode);
            } else {
                $this->mermaid[] = sprintf('%s-->%s', $sourceNode, $targetNode);
            }
        }
    }

    /**
     * @param ProcessNodeInterface $node
     */
    public function visitProcessNode(ProcessNodeInterface $node): void
    {
        $this->visitTransitionNode($node);
    }
}