<?php

namespace Dgame\Graph\Test;

use Dgame\Graph\Context;
use Dgame\Graph\Graph;
use Dgame\Graph\Trace\DefaultNodeStateTracer;
use PHPUnit\Framework\TestCase;

/**
 * Class NodeTest
 */
final class NodeTest extends TestCase
{
    private $graph;

    use NodeTestTrait;

    public function __construct()
    {
        parent::__construct();

        $this->graph = new Graph();
        $this->graph->setNode($this->createNode('a'));
        $this->graph->setNode($this->createNode('b'));
        $this->graph->setNode($this->createNode('c1'));
        $this->graph->setNode($this->createNode('c2'));
        $this->graph->setNode($this->createNode('d'));

        $this->graph->getNode('a')->setTransitionTo($this->graph->getNode('b'));
        $this->graph->getNode('b')->setTransitionTo($this->graph->getNode('c1'));
        $this->graph->getNode('b')->setTransitionTo($this->graph->getNode('c2'));
        $this->graph->getNode('c1')->setTransitionTo($this->graph->getNode('d'));
        $this->graph->getNode('c2')->setTransitionTo($this->graph->getNode('d'));
    }

    public function testFirstFullRoute()
    {
        $context = new Context();
        $context->setNodeStateTracer(new DefaultNodeStateTracer());
        $context->set('b', true);
        $context->set('c1', true);
        $context->set('d', true);
        $this->graph->getNode('a')->process($context);

        $this->assertEquals('a(0)->b(1)->c1(1)->d(1)', $context->getNodeStateTracer()->getTrace());
    }

    public function testFirstPartialRoute()
    {
        $context = new Context();
        $context->setNodeStateTracer(new DefaultNodeStateTracer());
        $context->set('b', true);
        $this->graph->getNode('a')->process($context);

        $this->assertEquals('a(0)->b(1)', $context->getNodeStateTracer()->getTrace());
    }

    public function testSecondtFullRoute()
    {
        $context = new Context();
        $context->setNodeStateTracer(new DefaultNodeStateTracer());
        $context->set('b', true);
        $context->set('c2', true);
        $context->set('d', true);
        $this->graph->getNode('a')->process($context);

        $this->assertEquals('a(0)->b(1)->c2(1)->d(1)', $context->getNodeStateTracer()->getTrace());
    }

    public function testSecondtPartialRoute()
    {
        $context = new Context();
        $context->setNodeStateTracer(new DefaultNodeStateTracer());
        $context->set('b', true);
        $context->set('c2', true);
        $this->graph->getNode('a')->process($context);

        $this->assertEquals('a(0)->b(1)->c2(1)', $context->getNodeStateTracer()->getTrace());
    }
}
