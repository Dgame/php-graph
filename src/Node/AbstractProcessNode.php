<?php

namespace Dgame\Graph\Node;

use Dgame\Graph\Context;
use Dgame\Graph\Trace\NodeStateTrace;

/**
 * Class AbstractProcessNode
 * @package Dgame\Graph\Node
 */
abstract class AbstractProcessNode extends TransitionNode implements ProcessNodeInterface
{
    /**
     * @param Context $context
     *
     * @return bool
     */
    abstract protected function execute(Context $context): bool;

    /**
     * @param Context $context
     *
     * @return bool
     */
    final public function process(Context $context): bool
    {
        $result = $this->execute($context);
        if ($context->hasNodeStateTracer()) {
            $context->getNodeStateTracer()->pushNodeState(new NodeStateTrace($this, $result, $context));
        }

        if (!$this->canFindTransition($context) && $context->hasLogger()) {
            $context->getLogger()->info('Es konnte kein Übergang gefunden werden');
        }

        return $result;
    }

    /**
     * @param Context $context
     *
     * @return bool
     */
    private function canFindTransition(Context $context): bool
    {
        $visitor = new ProcessNodeVisitor($context);
        foreach ($this->getTransitions() as $transition) {
            $node = $transition->getNode();
            $node->accept($visitor);
            if ($visitor->isMatched()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param NodeVisitorInterface $visitor
     */
    public function accept(NodeVisitorInterface $visitor): void
    {
        $visitor->visitProcessNode($this);
    }
}