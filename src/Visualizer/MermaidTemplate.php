<?php

namespace Dgame\Graph\Visualizer;

use Dgame\Graph\Graph;

/**
 * Class MermaidTemplate
 * @package Dgame\Graph\Visualizer
 */
final class MermaidTemplate
{
    private const BRACKET = '%s[%s]';
    private const ROUND   = '%s(%s)';
    private const DIAMOND = '%s{%s}';

    /**
     * @var string
     */
    private $template;

    /**
     * MermaidTemplate constructor.
     *
     * @param string $name
     * @param Graph  $graph
     */
    public function __construct(string $name, Graph $graph)
    {
        $this->visualizeNode($name, $graph);
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param string $name
     * @param Graph  $graph
     */
    public function visualizeNode(string $name, Graph $graph): void
    {
        $transitions = $graph->getTransitionNamesOfNode($name);
        $count       = count($transitions);
        if ($count !== 0) {
            $this->template = $count === 1 ? self::BRACKET : self::DIAMOND;
        } else {
            $this->template = self::ROUND;
        }
    }
}
