<?php

namespace Dgame\Graph\Exception;

use Dgame\Ensurance\Exception\EnsuranceFormatException;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class ItemNotFoundException
 * @package Dgame\Graph\Exception
 */
final class ItemNotFoundException extends EnsuranceFormatException implements NotFoundExceptionInterface
{
    /**
     * ItemNotFoundException constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct('Item "%s" was not found', $name);
    }
}
