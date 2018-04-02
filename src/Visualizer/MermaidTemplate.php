<?php

namespace Dgame\Graph\Visualizer;

use Dgame\Graph\Node\NodeInterface;
use Dgame\Graph\Node\NodeVisitorInterface;
use Dgame\Graph\Node\ProcessNodeInterface;
use Dgame\Graph\Node\TransitionNodeInterface;

/**
 * Class MermaidTemplate
 * @package Dgame\Graph\Visualizer
 */
final class MermaidTemplate implements NodeVisitorInterface
{
    private const BRACKET    = '%s[%s]';
    private const ROUND      = '%s(%s)';
    private const TRANSITION = '%s{%s}';

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
        $node->accept($this);
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
    public function visitNode(NodeInterface $node): void
    {
        $this->template = self::BRACKET;
    }

    /**
     * @param TransitionNodeInterface $node
     */
    public function visitTransitionNode(TransitionNodeInterface $node): void
    {
        if ($node->hasTransitions()) {
            $this->template = count($node->getTransitions()) === 1 ? self::BRACKET : self::TRANSITION;
        } else {
            $this->template = self::ROUND;
        }
    }

    /**
     * @param ProcessNodeInterface $node
     */
    public function visitProcessNode(ProcessNodeInterface $node): void
    {
        $this->visitTransitionNode($node);
    }
}