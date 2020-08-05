<?php

declare(strict_types=1);

namespace Estasi\Form\Traits;

use Estasi\Utility\Json;

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
    private function getAttributesAsIterable($attributes): iterable
    {
        if (isset($attributes)) {
            if (is_string($attributes)) {
                $attributes = Json::decode($attributes);
            } elseif (false === is_iterable($attributes)) {
                $attributes = [];
            }
        } else {
            $attributes = [];
        }
        
        return $attributes;
    }
}
