<?php

namespace Dgame\Graph;

/**
 * Interface AbortableInterface
 * @package Dgame\Graph
 */
interface AbortableInterface
{
    /**
     * @param string|null $message
     */
    public function abort(string $message = null): void;

    /**
     * @return bool
     */
    public function isAborted(): bool;

    /**
     * @return string
     */
    public function getAbortMessage(): string;
}
