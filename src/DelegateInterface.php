<?php

namespace Dgame\Graph;

/**
 * Interface DelegateInterface
 * @package Dgame\Graph
 */
interface DelegateInterface
{
    /**
     * @return callable
     */
    public function getClosure(): callable;

    /**
     * @return string
     */
    public function getName(): string;
}
