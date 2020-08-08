<?php

declare(strict_types=1);

namespace Estasi\Form\Traits;

use Estasi\Utility\Json;

use function is_string;

/**
 * Trait GetAttributesAsIterable
 *
 * @package Estasi\Form\Traits
 */
trait GetAttributesAsIterable
{
    /**
     * Returns a list of attributes taking any value as input
     *
     * @param mixed $attributes
     *
     * @return iterable
     * @throws \JsonException
     * @noinspection PhpUndefinedClassInspection
     */
    private function getAttributesAsIterable(string|iterable $attributes): iterable
    {
        return is_string($attributes) ? Json::decode($attributes) : $attributes;
    }
}
