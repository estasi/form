<?php

declare(strict_types=1);

namespace Estasi\Form\Traits;

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
     */
    private function getAttributesAsIterable($attributes): iterable
    {
        if (isset($attributes)) {
            if (is_string($attributes)) {
                $attributes = json_decode($attributes, true, JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING);
            } elseif (false === is_iterable($attributes)) {
                $attributes = [];
            }
        } else {
            $attributes = [];
        }

        return $attributes;
    }
}
