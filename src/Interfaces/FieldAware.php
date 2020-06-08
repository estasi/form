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
     * Returns an object that implements the interface \Estasi\Form\Interfaces\Field
     *
     * @return \Estasi\Form\Interfaces\Field
     */
    public function getField(): Field;
}
