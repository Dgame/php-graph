<?php

namespace Dgame\Graph\Visualizer;

use Dgame\Graph\Graph;

/**
 * Class MermaidVisualizer
 * @package Dgame\Graph\Visualizer
 */
final class MermaidVisualizer
{
    /**
     * @var Graph
     */
    private $graph;
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
        $this->graph = $graph;

        foreach ($graph->getNodeNames() as $name) {
            $this->visualizeNode($name);
        }
    }

    /**
     * @param string $source
     */
    private function visualizeNode(string $source): void
    {
        $sourceNode = $this->getMermaidNodeOf($source);
        foreach ($this->graph->getTransitionNamesOfNode($source) as $target) {
            $targetNode = $this->getMermaidNodeOf($target);
            $condition = $this->graph->getTransitionCondition([$source => $target]);
            $condition = empty($condition) ? '' : sprintf('|%s|', $condition);

            $this->mermaid[] = sprintf('%s-->%s%s', $sourceNode, $condition, $targetNode);
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
     * @param string $name
     *
     * @return string
     */
    private function getMermaidNodeOf(string $name): string
    {
        if ($this->hasAlias($name)) {
            return $this->getAliasOf($name);
        }

        $transitions = $this->graph->getTransitionNamesOfNode($name);
        if (empty($transitions)) {
            return $name;
        }

        $alias    = $this->getAliasOf($name);
        $template = new MermaidTemplate($name, $this->graph);

        $mermaidNode        = sprintf($template->getTemplate(), $alias, $name);
        $this->nodes[$name] = $mermaidNode;

        return $mermaidNode;
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
     * @param string $name
     *
     * @return string
     */
    private function getAliasOf(string $name): string
    {
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
