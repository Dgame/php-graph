<?php

namespace Dgame\Graph;

use Exception;

/**
 * Class Graph
 * @package Dgame\Graph
 */
final class Graph
{
    /**
     * @var NodeInterface[]
     */
    private $nodes = [];

    /**
     * @return NodeInterface[]
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }

    /**
     * @param string $name
     *
     * @return NodeInterface
     * @throws Exception
     */
    private function getNodeByName(string $name): NodeInterface
    {
        foreach ($this->nodes as $index => $node) {
            if ($node->getName() === $name) {
                return $node;
            }
        }

        throw new Exception('No node with name ', $name);
    }

    /**
     * @param callable    $closure
     * @param string|null $name
     */
    public function insert(callable $closure, string $name = null): void
    {
        $this->nodes[] = new Node($name ?? uniqid(), $closure);
    }

    /**
     * @param string $from
     * @param string $to
     *
     * @throws Exception
     */
    public function setTransition(string $from, string $to): void
    {
        $lhs = $this->getNodeByName($from);
        $rhs = $this->getNodeByName($to);

        $lhs->setTransitionTo($rhs);
    }

    /**
     * @param string  $name
     * @param Context $context
     *
     * @throws Exception
     */
    public function traverse(string $name, Context $context): void
    {
        $this->getNodeByName($name)->executeWith($context);
    }

    /**
     * @param string $source
     *
     * @return bool
     * @throws Exception
     */
    public function isCyclic(string $source): bool
    {
        $isCyclic = function (array $nodes, array $visited) use (&$isCyclic): bool {
            foreach ($nodes as $node) {
                $name = $node->getName();
                if (array_key_exists($name, $visited)) {
                    return true;
                }

                $visited[$name] = true;

                if ($isCyclic($node->getTransitions(), $visited)) {
                    return true;
                }
            }

            return false;
        };

        $node = $this->getNodeByName($source);

        return $isCyclic($node->getTransitions(), []);
    }

    /**
     * @param string $source
     * @param string $target
     *
     * @return bool
     * @throws Exception
     */
    public function canReach(string $source, string $target): bool
    {
        $canReach = function (array $nodes, array $visited) use (&$canReach, $target): bool {
            foreach ($nodes as $node) {
                $name = $node->getName();
                if (array_key_exists($name, $visited)) {
                    continue;
                }

                $visited[$name] = true;

                if ($name === $target) {
                    return true;
                }

                if ($canReach($node->getTransitions(), $visited)) {
                    return true;
                }
            }

            return false;
        };

        $node = $this->getNodeByName($source);

        return $canReach($node->getTransitions(), []);
    }

    /**
     * @return bool
     */
    public function isConnected(): bool
    {
        $isConnected = function (array $nodes, array $visited) use (&$isConnected, $source): bool {
            foreach ($nodes as $node) {
                $name = $node->getName();
                if (array_key_exists($name, $visited)) {
                    continue;
                }

                $visited[$name] = true;

                if (!$this->canReach($source, $name)) {
                    return false;
                }

                if ($isConnected($node->getTransitions(), $visited)) {
                    return true;
                }
            }

            return true;
        };

        return $isConnected($this->nodes, []);
    }

    /**
     * @param string $source
     *
     * @return array
     * @throws Exception
     */
    public function getTargets(string $source): array
    {
        $targets = [];

        $traverse = function (array $nodes, array $visited) use (&$traverse, &$targets) {
            foreach ($nodes as $node) {
                $name = $node->getName();
                if (array_key_exists($name, $visited)) {
                    continue;
                }

                $visited[$name] = true;

                if (!$node->hasTransitions()) {
                    $targets[] = $name;
                } else {
                    $traverse($node->getTransitions(), $visited);
                }
            }
        };

        $node = $this->getNodeByName($source);
        $traverse($node->getTransitions(), []);

        return $targets;
    }
}
