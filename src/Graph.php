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
     * @var array
     */
    private $nodes = [];
    /**
     * @var array
     */
    private $transitions = [];
    /**
     * @var array
     */
    private $transitionConditions = [];

    /**
     * @return string[]
     */
    public function getNodeNames(): array
    {
        return array_keys($this->nodes);
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function getTransitionNamesOfNode(string $name): array
    {
        return array_keys($this->getTransitionsOfNode($name));
    }

    /**
     * @param array $transition
     *
     * @return string
     */
    public function getTransitionCondition(array $transition): string
    {
        $source = key($transition);
        $target = current($transition);

        return $this->transitionConditions[$source][$target] ?? '';
    }

    /**
     * @param string $name
     *
     * @return array
     */
    private function getTransitionsOfNode(string $name): array
    {
        return $this->transitions[$name] ?? [];
    }

    /**
     * @param string   $name
     * @param callable $closure
     */
    public function insert(string $name, callable $closure): void
    {
        $this->nodes[$name] = $closure;
    }

    /**
     * @param array         $transitions
     * @param callable|null $condition
     */
    public function setTransitions(array $transitions, callable $condition = null)
    {
        $condition = $condition ?? function (): bool {
            return true;
        };

        foreach ($transitions as $source => $targets) {
            $source = trim($source);
            assert(!empty($source));

            $targets = is_array($targets) ? $targets : [$targets];
            foreach ($targets as $key => $target) {
                if (is_int($key)) {
                    $this->transitions[$source][$target] = $condition;
                } elseif (is_bool($target)) {
                    $this->setTransitionCondition([$source => $key], var_export($target, true));

                    $this->setTransitions([$source => $key], function (Context $context) use ($source, $condition, $target): bool {
                        return $context->getFromHistory($source) === $target && $condition($context);
                    });
                } elseif (is_callable($target)) {
                    $this->setTransitionCondition([$source => $key], '?');

                    $this->setTransitions([$source => $key], function (Context $context) use ($condition, $target): bool {
                        return $condition($context) && $target($context);
                    });
                } else {
                    $target = trim($target);
                    assert(!empty($target));

                    $this->setTransitionCondition([$source => $key], 'if ' . $target);

                    $this->setTransitions([$source => $key], function (Context $context) use ($source, $condition, $target): bool {
                        $value = true;
                        if ($target[0] === '!') {
                            $target = substr($target, 1);
                            $value  = false;
                        }

                        return $context->getFromHistory($target) === $value && $condition($context);
                    });
                }
            }
        }
    }

    /**
     * @param array  $transition
     * @param string $condition
     */
    private function setTransitionCondition(array $transition, string $condition): void
    {
        $source = key($transition);
        $target = current($transition);

        if (!array_key_exists($source, $this->transitionConditions)) {
            $this->transitionConditions[$source] = [];
        }

        $this->transitionConditions[$source][$target] = $condition;
    }

    /**
     * @param string       $key
     * @param Context|null $context
     *
     * @return Context
     * @throws Exception
     */
    public function launch(string $key, Context $context = null): Context
    {
        $uuid = bin2hex(random_bytes(16));

        $context = $context ?? new Context();
        $context->clearHistory();
        $context->setUuid($uuid);

        $this->trigger($key, $context);

        return $context;
    }

    /**
     * @param string  $key
     * @param Context $context
     *
     * @throws Exception
     */
    private function trigger(string $key, Context $context): void
    {
        $result = $this->execute($key, $context);
        $context->setHistory($key, $result);

        foreach ($this->getTransitionsOfNode($key) as $key => $condition) {
            if ($condition($context)) {
                $this->trigger($key, $context);
            }
        }
    }

    /**
     * @param string  $key
     * @param Context $context
     *
     * @return bool
     */
    public function execute(string $key, Context $context): bool
    {
        $closure = $this->nodes[$key];

        return $closure($context) !== false;
    }

    /**
     * @param string $source
     *
     * @return bool
     * @throws Exception
     */
    public function isCyclic(string $source): bool
    {
        $isCyclic    = function (array $transitions, array $visited) use (&$isCyclic): bool {
            foreach (array_keys($transitions) as $key) {
                if (array_key_exists($key, $visited)) {
                    return true;
                }

                $visited[$key] = true;
                $transitions   = $this->getTransitionsOfNode($key);
                if ($isCyclic($transitions, $visited)) {
                    return true;
                }
            }

            return false;
        };
        $transitions = $this->getTransitionsOfNode($source);

        return $isCyclic($transitions, []);
    }

    /**
     * @param array $transition
     *
     * @return bool
     */
    public function canReach(array $transition): bool
    {
        $source = key($transition);
        $target = current($transition);

        $canReach    = function (array $transitions, array $visited) use (&$canReach, $target): bool {
            foreach (array_keys($transitions) as $key) {
                if (array_key_exists($key, $visited)) {
                    continue;
                }

                $visited[$key] = true;
                if ($key === $target) {
                    return true;
                }

                $transitions = $this->getTransitionsOfNode($key);
                if ($canReach($transitions, $visited)) {
                    return true;
                }
            }

            return false;
        };
        $transitions = $this->getTransitionsOfNode($source);

        return $canReach($transitions, []);
    }

    /**
     * @param string $source
     *
     * @return bool
     */
    public function isComplete(string $source): bool
    {
        $isConnected = function (array $nodes, array $visited) use (&$isConnected, $source): bool {
            foreach (array_keys($nodes) as $key) {
                if (array_key_exists($key, $visited)) {
                    continue;
                }

                $visited[$key] = true;
                if (!$this->canReach([$source => $key])) {
                    return false;
                }

                $transitions = $this->getTransitionsOfNode($key);
                if ($isConnected($transitions, $visited)) {
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
        $targets     = [];
        $traverse    = function (array $transitions, array $visited) use (&$traverse, &$targets) {
            foreach (array_keys($transitions) as $key) {
                if (array_key_exists($key, $visited)) {
                    continue;
                }

                $visited[$key] = true;
                $transitions   = $this->getTransitionsOfNode($key);
                if (empty($transitions)) {
                    $targets[] = $key;
                } else {
                    $traverse($transitions, $visited);
                }
            }
        };
        $transitions = $this->getTransitionsOfNode($source);
        $traverse($transitions, []);

        return $targets;
    }
}
