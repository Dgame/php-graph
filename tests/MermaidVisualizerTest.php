<?php

namespace Dgame\Graph\Test;

use Dgame\Graph\Graph;
use Dgame\Graph\Visualizer\MermaidVisualizer;
use PHPUnit\Framework\TestCase;

/**
 * Class MermaidVisualizerTest
 * @package Dgame\Graph\Test
 */
final class MermaidVisualizerTest extends TestCase
{
    public function testEmptyMermaidDiagram()
    {
        $graph = new Graph();
        $graph->insert('A', function () {
            print 'A' . PHP_EOL;
        });
        $graph->insert('B', function () {
            print 'B' . PHP_EOL;
        });
        $graph->insert('C', function () {
            print 'C' . PHP_EOL;
        });

        $visualizer = new MermaidVisualizer($graph);
        $this->assertEmpty($visualizer->getMermaidDiagram());
    }

    public function testMermaidDiagram()
    {
        $graph = new Graph();
        $graph->insert('A', function () {
            print 'A' . PHP_EOL;
        });
        $graph->insert('B', function () {
            print 'B' . PHP_EOL;
        });
        $graph->insert('C', function () {
            print 'C' . PHP_EOL;
        });

        $graph->setTransitions(['A' => 'C']);
        $graph->setTransitions(['C' => 'B']);

        $visualizer = new MermaidVisualizer($graph);
        $this->assertEquals(['graph TD', '__A_1[A]-->__B_1[C]', '__B_1-->B'], $visualizer->getMermaidDiagram());
    }

    public function testCyclicMermaidDiagram()
    {
        $graph = new Graph();
        $graph->insert('A', function () {
            print 'A' . PHP_EOL;
        });
        $graph->insert('B', function () {
            print 'B' . PHP_EOL;
        });
        $graph->insert('C', function () {
            print 'C' . PHP_EOL;
        });

        $graph->setTransitions(['A' => 'C']);
        $graph->setTransitions(['C' => 'B']);
        $graph->setTransitions(['B' => 'A']);

        $visualizer = new MermaidVisualizer($graph);
        $this->assertEquals(['graph TD', '__A_1[A]-->__B_1[C]', '__C_1[B]-->__A_1', '__B_1-->__C_1'], $visualizer->getMermaidDiagram());
    }
}
