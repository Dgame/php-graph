<?php

namespace Dgame\Graph\Node;

/**
 * Class Transition
 * @package Dgame\Graph\Node
 */
final class Transition
{
    /**
     * @var NodeInterface
     */
    private $node;
    /**
     * @var string|null
     */
    private $description;

    /**
     * Transition constructor.
     *
     * @param NodeInterface $node
     * @param string|null   $description
     */
    public function __construct(NodeInterface $node, string $description = null)
    {
        $this->node = $node;
        $this->setDescription($description);
    }

    /**
     * @return NodeInterface
     */
    public function getNode(): NodeInterface
    {
        return $this->node;
    }

    /**
     * @param NodeInterface $node
     *
     * @return bool
     */
    public function isTransitionTo(NodeInterface $node): bool
    {
        return $this->node === $node;
    }

    /**
     * @param null|string $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return bool
     */
    public function hasDescription(): bool
    {
        return !empty($this->description);
    }

    /**
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }
}