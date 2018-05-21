<?php

namespace Dgame\Graph;

use Dgame\Graph\Exception\ItemNotFoundException;
use Psr\Container\ContainerInterface;
use function Dgame\Ensurance\enforce;

/**
 * Class Context
 * @package Dgame\Graph
 */
final class Context implements ContainerInterface
{
    /**
     * @var array
     */
    private $context = [];

    /**
     * Context constructor.
     *
     * @param array $context
     */
    public function __construct(array $context = [])
    {
        $this->context = $context;
    }

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
     * @param        $value
     */
    public function appendTo(string $name, $value): void
    {
        $this->context[$name][] = $value;
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
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getOrDefault(string $name, $default)
    {
        return $this->has($name) ? $this->get($name) : $default;
    }

    /**
     * @param string $name
     * @param        $value
     *
     * @return mixed
     */
    public function getOrSet(string $name, $value)
    {
        if (!$this->has($name)) {
            $this->set($name, $value);
        }

        return $this->get($name);
    }

    /**
     * @param string $name
     *
     * @return int
     */
    public function getAsInt(string $name): int
    {
        $value = $this->getOrDefault($name, 0);

        return filter_var($value, FILTER_VALIDATE_INT, ['options' => ['default' => 0]]);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function getAsBool(string $name): bool
    {
        $value = $this->getOrDefault($name, false);

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
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
}
