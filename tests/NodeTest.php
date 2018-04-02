<?php

use Dgame\Graph\Context;
use Dgame\Graph\Graph;
use Dgame\Graph\Node\AbstractProcessNode;
use Dgame\Graph\Node\ProcessNodeInterface;
use Dgame\Graph\Node\TransitionNodeInterface;
use Dgame\Graph\Trace\DefaultNodeStateTracer;
use Dgame\Graph\Visualizer\MermaidGraphVisualizer;
use PHPUnit\Framework\TestCase;

/**
 * Class NodeTest
 */
final class NodeTest extends TestCase
{
    /**
     * @var ProcessNodeInterface[]
     */
    private $nodes = [];

    public function __construct()
    {
        parent::__construct();

        $this->getNode('a')->setTransitionTo($this->getNode('b'));
        $this->getNode('b')->setTransitionTo($this->getNode('c1'));
        $this->getNode('b')->setTransitionTo($this->getNode('c2'));
        $this->getNode('c1')->setTransitionTo($this->getNode('d'));
        $this->getNode('c2')->setTransitionTo($this->getNode('d'));
    }

    private function getNode(string $name): TransitionNodeInterface
    {
        if (!array_key_exists($name, $this->nodes)) {
            $this->nodes[$name] = new class(strtoupper($name)) extends AbstractProcessNode
            {
                public function isFulfilledBy(Context $context): bool
                {
                    return $context->getAsBool($this->getName());
                }

                protected function execute(Context $context): bool
                {
                    return $context->getAsBool($this->getName());
                }
            };
        }

        return $this->nodes[$name];
    }

    public function testFirstFullRoute()
    {
        $context = new Context();
        $context->setNodeStateTracer(new DefaultNodeStateTracer());
        $context->set('B', true);
        $context->set('C1', true);
        $context->set('D', true);
        $this->nodes['a']->process($context);

        $this->assertEquals('A(0)->B(1)->C1(1)->D(1)', $context->getNodeStateTracer()->getTrace());
    }

    public function testFirstPartialRoute()
    {
        $context = new Context();
        $context->setNodeStateTracer(new DefaultNodeStateTracer());
        $context->set('B', true);
        $this->nodes['a']->process($context);

        $this->assertEquals('A(0)->B(1)', $context->getNodeStateTracer()->getTrace());
    }

    public function testSecondtFullRoute()
    {
        $context = new Context();
        $context->setNodeStateTracer(new DefaultNodeStateTracer());
        $context->set('B', true);
        $context->set('C2', true);
        $context->set('D', true);
        $this->nodes['a']->process($context);

        $this->assertEquals('A(0)->B(1)->C2(1)->D(1)', $context->getNodeStateTracer()->getTrace());
    }

    public function testSecondtPartialRoute()
    {
        $context = new Context();
        $context->setNodeStateTracer(new DefaultNodeStateTracer());
        $context->set('B', true);
        $context->set('C2', true);
        $this->nodes['a']->process($context);

        $this->assertEquals('A(0)->B(1)->C2(1)', $context->getNodeStateTracer()->getTrace());
    }

    public function testMermaidVisualizing()
    {
        $graph      = new Graph($this->nodes);
        $visualizer = new MermaidGraphVisualizer($graph);

        $exptected = [
            'A1[A]-->B1{B}',
            'B1{B}-->C1[C1]',
            'B1{B}-->D1[C2]',
            'C1[C1]-->E1(D)',
            'D1[C2]-->E1(D)',
        ];

        $this->assertEquals($exptected, $visualizer->getVisualized());
    }

    public function testForwardGraph()
    {
        $graph      = new Graph();
        $graph->setNode($this->getNode('z'));
        $graph->setNode($this->getNode('y'));
        $graph->setNode($this->getNode('x'));
        $graph->setForwardCycleTransition();

        $visualizer = new MermaidGraphVisualizer($graph);
        print_r($visualizer->getVisualized());
        exit;

        $exptected = [
            'A1[A]-->B1{B}',
            'B1{B}-->C1[C1]',
            'B1{B}-->D1[C2]',
            'C1[C1]-->E1(D)',
            'D1[C2]-->E1(D)',
        ];

        $this->assertEquals($exptected, $visualizer->getVisualized());
    }
}