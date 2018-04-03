<?php

namespace Dgame\Graph\Node;

use Dgame\Graph\Context;

/**
 * Interface ProcessNodeInterface
 * @package Dgame\Graph\Node
 */
interface ProcessNodeInterface extends TransitionNodeInterface
{
    /**
     * @param Context $context
     *
     * @return bool
     */
    public function isFulfilledBy(Context $context): bool;

    /**
     * @param Context $context
     *
     * @return bool
     */
    public function process(Context $context): bool;
}
