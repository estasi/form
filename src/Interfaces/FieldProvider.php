<?php

declare(strict_types=1);

namespace Estasi\Form\Interfaces;

/**
 * Interface FieldProvider
 *
 * @package Estasi\Form\Interfaces
 */
interface FieldProvider
{
    /**
     * Returns data for creating a Field as an iterable object or array, in the key=>value format
     *
     * @return iterable<string, mixed>
     */
    public function getSpecification(): iterable;
}
