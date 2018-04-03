<?php

namespace Dgame\Graph\Test;

use Dgame\Graph\Graph;
use Dgame\Graph\Visualizer\MermaidVisualizer;
use PHPUnit\Framework\TestCase;

/**
 * Class GraphTest
 */
final class GraphTest extends TestCase
{
    use NodeTestTrait;

    public function testMermaidVisualizing()
    {
        $graph = new Graph();
        $graph->setNode($this->createNode('a'));
        $graph->setNode($this->createNode('b'));
        $graph->setNode($this->createNode('c1'));
        $graph->setNode($this->createNode('c2'));
        $graph->setNode($this->createNode('d'));

        $graph->getNode('a')->setTransitionTo($graph->getNode('b'));
        $graph->getNode('b')->setTransitionTo($graph->getNode('c1'));
        $graph->getNode('b')->setTransitionTo($graph->getNode('c2'));
        $graph->getNode('c1')->setTransitionTo($graph->getNode('d'));
        $graph->getNode('c2')->setTransitionTo($graph->getNode('d'));

        $visualizer = new MermaidVisualizer($graph);

        $exptected = [
            'A1[a]-->B1{b}',
            'B1-->C1[c1]',
            'B1-->D1[c2]',
            'C1-->E1(d)',
            'D1-->E1',
        ];

        $this->assertEquals($exptected, $visualizer->getVisualized());
    }

    public function testForwardTransition()
    {
        $graph      = new Graph();
        $graph->setNode($this->createNode('x'));
        $graph->setNode($this->createNode('y'));
        $graph->setNode($this->createNode('z'));
        $graph->setForwardCycleTransition();

        $visualizer = new MermaidVisualizer($graph);
        $exptected  = [
            'A1[x]-->B1[y]',
            'B1-->C1[z]',
            'C1-->A1'
        ];

        $this->assertEquals($exptected, $visualizer->getVisualized());
    }

    public function testBAckwardTransition()
    {
        $graph      = new Graph();
        $graph->setNode($this->createNode('x'));
        $graph->setNode($this->createNode('y'));
        $graph->setNode($this->createNode('z'));
        $graph->setBackwardCycleTransition();

        $visualizer = new MermaidVisualizer($graph);
        $exptected  = [
            'A1[x]-->B1[z]',
            'C1[y]-->A1',
            'B1-->C1'
        ];

        $this->assertEquals($exptected, $visualizer->getVisualized());
    }
}
