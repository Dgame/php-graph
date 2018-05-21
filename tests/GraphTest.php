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
    public function testTraverse()
    {
        $graph = new Graph();
        $graph->insert(function () {
            print $this->getName() . PHP_EOL;
        }, 'A');
        $graph->insert(function () {
            print $this->getName() . PHP_EOL;
        }, 'B');
        $graph->insert(function () {
            print $this->getName() . PHP_EOL;
        }, 'C');

        $graph->setTransition('A', 'C');
        $graph->setTransition('C', 'B');

        $context = new Context();
        ob_start();
        $graph->traverse('A', $context);
        $content = ob_get_clean();

        $this->assertEquals(['A', 'C', 'B'], explode(PHP_EOL, trim($content)));
    }

    public function testIsCyclic()
    {
        $graph = new Graph();
        $graph->insert(function () {
        }, 'A');
        $graph->insert(function () {
        }, 'B');
        $graph->insert(function () {
        }, 'C');

        $graph->setTransition('A', 'C');
        $graph->setTransition('C', 'B');

        $this->assertFalse($graph->isCyclic('A'));
        $this->assertFalse($graph->isCyclic('B'));
        $this->assertFalse($graph->isCyclic('C'));

        $graph->setTransition('B', 'A');

        $this->assertTrue($graph->isCyclic('A'));
        $this->assertTrue($graph->isCyclic('B'));
        $this->assertTrue($graph->isCyclic('C'));
    }

    public function testCanReach()
    {
        $graph = new Graph();
        $graph->insert(function () {
        }, 'A');
        $graph->insert(function () {
        }, 'B');
        $graph->insert(function () {
        }, 'C');

        $graph->setTransition('A', 'C');

        $this->assertFalse($graph->canReach('A', 'B'));
        $this->assertTrue($graph->canReach('A', 'C'));

        $graph->setTransition('C', 'B');

        $this->assertTrue($graph->canReach('A', 'B'));
    }

    public function testGetTargets()
    {
        $graph = new Graph();
        $graph->insert(function () {
        }, 'A');
        $graph->insert(function () {
        }, 'B');
        $graph->insert(function () {
        }, 'C');

        $graph->setTransition('A', 'C');
        $graph->setTransition('C', 'B');

        $this->assertEquals(['B'], $graph->getTargets('A'));
        $this->assertEmpty($graph->getTargets('B'));
        $this->assertEquals(['B'], $graph->getTargets('C'));

        $graph->setTransition('B', 'A');

        $this->assertEmpty($graph->getTargets('A'));
        $this->assertEmpty($graph->getTargets('B'));
        $this->assertEmpty($graph->getTargets('C'));
    }
}
