<?php

namespace Dgame\Graph\Visualizer;

use Dgame\Graph\NodeInterface;

/**
 * Class MermaidTemplate
 * @package Dgame\Graph\Visualizer
 */
final class MermaidTemplate
{
    private const BRACKET = '%s[%s]';
    private const ROUND   = '%s(%s)';
    private const DIAMOND = '%s{%s}';

    /**
     * @var string
     */
    private $template;

    /**
     * MermaidTemplate constructor.
     *
     * @param NodeInterface $node
     */
    public function __construct(NodeInterface $node)
    {
        $this->visualizeNode($node);
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param NodeInterface $node
     */
    public function visualizeNode(NodeInterface $node): void
    {
        if ($node->hasTransitions()) {
            $this->template = count($node->getTransitions()) === 1 ? self::BRACKET : self::DIAMOND;
        } else {
            $this->template = self::ROUND;
        }
    }
}
