<?php

declare(strict_types=1);

namespace Estasi\Form\Interfaces;

/**
 * Interface FieldAware
 *
 * @package Estasi\Form\Interfaces
 */
interface FieldAware
{
    /**
     * Returns an object that implements the interface \Estasi\Form\Interfaces\Input
     *
     * @return \App\Form\Interfaces\Field|\Estasi\Form\Interfaces\FieldGroup
     */
    public function getField(): Field|FieldGroup;
}
