<?php

namespace Dgame\Graph\Test;

use Dgame\Graph\Context;
use Dgame\Graph\Node\AbstractProcessNode;
use Dgame\Graph\Node\ProcessNodeInterface;

/**
 * Trait NodeTestTrait
 * @package Dgame\Graph\Test
 */
trait NodeTestTrait
{
    /**
     * @var ProcessNodeInterface[]
     */
    private $nodes = [];

    /**
     * @param string $name
     *
     * @return ProcessNodeInterface
     */
    private function createNode(string $name): ProcessNodeInterface
    {
        return new class($name) extends AbstractProcessNode {
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
}
