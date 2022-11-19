<?php

namespace Dgame\Graph;

use function Dgame\Ensurance\enforce;
use Dgame\Graph\Exception\ItemNotFoundException;
use Psr\Container\ContainerInterface;

/**
 * Class Context
 * @package Dgame\Graph
 */
final class Context implements ContainerInterface
{
    private const HISTORY = 'history';

    /**
     * @var array
     */
    private $context = [];
    /**
     * @var string
     */
    private $uuid;

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
     * @param int    $default
     *
     * @return int
     */
    public function getAsInt(string $name, int $default = 0): int
    {
        $value = $this->getOrDefault($name, $default);

        return filter_var($value, FILTER_VALIDATE_INT, ['options' => ['default' => $default]]);
    }

    /**
     * @param string $name
     * @param bool   $default
     *
     * @return bool
     */
    public function getAsBool(string $name, bool $default = false): bool
    {
        $value = $this->getOrDefault($name, $default);

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
     * @param string|null $key
     */
    public function clear(string $key = null): void
    {
        if ($key === null) {
            $this->context = [];
        } elseif ($this->has($key)) {
            unset($this->context[$key]);
        }
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     */
    public function setUuid(string $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @param string $name
     * @param bool   $result
     */
    public function setHistory(string $name, bool $result): void
    {
        $this->context[self::HISTORY][$name] = $result;
    }

    /**
     *
     */
    public function clearHistory(): void
    {
        $this->clear(self::HISTORY);
    }

    /**
     * @return array
     */
    public function getHistory(): array
    {
        return $this->getOrDefault(self::HISTORY, []);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function getFromHistory(string $key): bool
    {
        return $this->getHistory()[$key] ?? false;
    }
}
