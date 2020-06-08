<?php

declare(strict_types=1);

namespace Estasi\Form\Interfaces;

/**
 * Interface FormProvider
 *
 * @package Estasi\Form\Interfaces
 */
interface FormProvider
{
    /**
     * Returns data for creating a Form as an iterable object or array, in the key=>value format
     *
     * @return iterable<string, mixed>
     */
    public function getSpecification(): iterable;
}
