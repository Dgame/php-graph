<?php

namespace Dgame\Graph\Node;

/**
 * Interface NodeInterface
 * @package Dgame\Graph\Node
 */
interface NodeInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param null|string $description
     */
    public function setDescription(?string $description): void;

    /**
     * @return bool
     */
    public function hasDescription(): bool;

    /**
     * @return null|string
     */
    public function getDescription(): ?string;

    /**
     * @param NodeVisitorInterface $visitor
     */
    public function accept(NodeVisitorInterface $visitor): void;
}
