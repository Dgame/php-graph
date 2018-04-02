<?php

namespace Dgame\Graph;

use Dgame\Graph\Exception\ItemNotFoundException;
use Dgame\Graph\Trace\NodeStateTracerInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use function Dgame\Ensurance\enforce;

/**
 * Class Context
 * @package Dgame\Graph
 */
final class Context implements ContainerInterface, AbortableInterface
{
    /**
     * @var array
     */
    private $context = [];
    /**
     * @var bool
     */
    private $aborted = false;
    /**
     * @var string|null
     */
    private $message;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var NodeStateTracerInterface
     */
    private $tracer;

    /**
     * @param string $name
     * @param        $value
     */
    public function set(string $name, $value): void
    {
        $this->context[$name] = $value;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has($name): bool
    {
        return array_key_exists($name, $this->context);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function get($name)
    {
        enforce($this->has($name))->setThrowable(new ItemNotFoundException($name));

        return $this->context[$name];
    }

    /**
     * @param string $name
     * @param null   $default
     *
     * @return mixed|null
     */
    public function getOrDefault(string $name, $default = null)
    {
        return $this->has($name) ? $this->get($name) : $default;
    }

    /**
     * @param string|null $message
     */
    public function abort(string $message = null): void
    {
        $this->aborted = true;
        $this->message = $message;
    }

    /**
     * @return bool
     */
    public function isAborted(): bool
    {
        return $this->aborted;
    }

    /**
     * @return string
     */
    public function getAbortMessage(): string
    {
        return $this->message ?? '';
    }

    /**
     * @param string $name
     *
     * @return int
     */
    public function getAsInt(string $name): int
    {
        return $this->getOrDefault($name, 0);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function getAsBool(string $name): bool
    {
        return $this->getOrDefault($name, false);
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        return $this->context;
    }

    /**
     *
     */
    public function clear(): void
    {
        $this->context = [];
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @return bool
     */
    public function hasLogger(): bool
    {
        return $this->logger !== null;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @param NodeStateTracerInterface $tracer
     */
    public function setNodeStateTracer(NodeStateTracerInterface $tracer): void
    {
        $this->tracer = $tracer;
    }

    /**
     * @return bool
     */
    public function hasNodeStateTracer(): bool
    {
        return $this->tracer !== null;
    }

    /**
     * @return NodeStateTracerInterface
     */
    public function getNodeStateTracer(): NodeStateTracerInterface
    {
        return $this->tracer;
    }
}