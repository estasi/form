<?php

declare(strict_types=1);

namespace Estasi\Form\Utility;

use Ds\Map;
use Estasi\Filter\KebabCase;

use function sprintf;

/**
 * Class ConvertAttributesToString
 *
 * @package Estasi\Form\Utility
 */
class ConvertAttributesToString
{
    private iterable $attributes;

    /**
     * ConvertAttributesToString constructor.
     *
     * @param iterable<string, string|int|float|bool> $attributes
     */
    public function __construct(iterable $attributes)
    {
        $this->attributes = new Map($attributes);
    }

    /** @noinspection PhpUnusedLocalVariableInspection */
    public function toString(): string
    {
        $kebabCase = new KebabCase();

        return $this->attributes->filter()
                                ->reduce(
                                    fn($carry, string $name, $value): string => $carry .= ' ' . (true === $value
                                            ? $kebabCase($name)
                                            : sprintf('%s="%s"', $kebabCase($name), $value)),
                                    ''
                                );
    }

    public function __toString()
    {
        return $this->toString();
    }
}
