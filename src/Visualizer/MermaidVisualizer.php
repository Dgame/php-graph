<?php

namespace Dgame\Graph\Visualizer;

use Dgame\Graph\Graph;
use Dgame\Graph\NodeInterface;

/**
 * Class MermaidVisualizer
 * @package Dgame\Graph\Visualizer
 */
final class MermaidVisualizer
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
            $this->visualizeNode($node);
        }
    }

    /**
     * @param NodeInterface $node
     */
    private function visualizeNode(NodeInterface $node): void
    {
        $sourceNode = $this->getMermaidNodeOf($node);
        foreach ($node->getTransitions() as $node) {
            $targetNode      = $this->getMermaidNodeOf($node);
            $this->mermaid[] = sprintf('%s-->%s', $sourceNode, $targetNode);
        }
    }

    /**
     * @param string $direction
     *
     * @return array
     */
    public function getMermaidDiagram(string $direction = 'TD'): array
    {
        if (!empty($this->mermaid)) {
            array_unshift($this->mermaid, 'graph ' . $direction);
        }

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

        if (!$node->hasTransitions()) {
            return $name;
        }

        $alias    = $this->getAliasOf($node);
        $template = new MermaidTemplate($node);

        $mermaidNode        = sprintf($template->getTemplate(), $alias, $this->getMermaidNodeContentOf($node));
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
        return $node->getName();
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

        return sprintf('__%s_%d', $letter, $this->index);
    }
}
