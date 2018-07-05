<?php

namespace Dgame\Graph\Test;

use Dgame\Graph\Context;
use Dgame\Graph\Graph;
use PHPUnit\Framework\TestCase;

/**
 * Class GraphTest
 */
final class GraphTest extends TestCase
{
    /**
     *
     */
    public function testTraverse()
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

        $context = new Context();
        ob_start();
        $graph->launch('A', $context);
        $content = ob_get_clean();

        $this->assertEquals(['A', 'C', 'B'], explode(PHP_EOL, trim($content)));
    }

    /**
     * @throws \Exception
     */
    public function testIsCyclic()
    {
        $graph = new Graph();
        $graph->insert('A', function () {
        });
        $graph->insert('B', function () {
        });
        $graph->insert('C', function () {
        });

        $graph->setTransitions(['A' => 'C', 'C' => 'B']);

        $this->assertFalse($graph->isCyclic('A'));
        $this->assertFalse($graph->isCyclic('B'));
        $this->assertFalse($graph->isCyclic('C'));

        $graph->setTransitions(['B' => 'A']);

        $this->assertTrue($graph->isCyclic('A'));
        $this->assertTrue($graph->isCyclic('B'));
        $this->assertTrue($graph->isCyclic('C'));
    }

    /**
     * @throws \Exception
     */
    public function testCanReach()
    {
        $graph = new Graph();
        $graph->insert('A', function () {
        });
        $graph->insert('B', function () {
        });
        $graph->insert('C', function () {
        });

        $graph->setTransitions(['A' => 'C']);

        $this->assertFalse($graph->canReach(['A' => 'B']));
        $this->assertTrue($graph->canReach(['A' => 'C']));

        $graph->setTransitions(['C' => 'B']);

        $this->assertTrue($graph->canReach(['A' => 'B']));
    }

    /**
     * @throws \Exception
     */
    public function testGetTargets()
    {
        $graph = new Graph();
        $graph->insert('A', function () {
        });
        $graph->insert('B', function () {
        });
        $graph->insert('C', function () {
        });

        $graph->setTransitions(['A' => 'C']);
        $graph->setTransitions(['C' => 'B']);

        $this->assertEquals(['B'], $graph->getTargets('A'));
        $this->assertEmpty($graph->getTargets('B'));
        $this->assertEquals(['B'], $graph->getTargets('C'));

        $graph->setTransitions(['B' => 'A']);

        $this->assertEmpty($graph->getTargets('A'));
        $this->assertEmpty($graph->getTargets('B'));
        $this->assertEmpty($graph->getTargets('C'));
    }
}
