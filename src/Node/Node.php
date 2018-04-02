<?php

namespace Dgame\Graph\Node;

/**
 * Class Node
 * @package Dgame\Graph\Node
 */
class Node implements NodeInterface
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string|null
     */
    private $description;

    /**
     * Node constructor.
     *
     * @param string|null $name
     */
    public function __construct(string $name = null)
    {
        $this->name = $name ?? static::class;
    }

    /**
     * @return string
     */
    final public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param null|string $description
     */
    final public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return bool
     */
    final public function hasDescription(): bool
    {
        return !empty($this->description);
    }

    /**
     * @return null|string
     */
    final public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param NodeVisitorInterface $visitor
     */
    public function accept(NodeVisitorInterface $visitor): void
    {
        $visitor->visitNode($this);
    }
}