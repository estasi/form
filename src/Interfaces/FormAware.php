<?php

declare(strict_types=1);

namespace Estasi\Form\Interfaces;

/**
 * Interface FormAware
 *
 * @package Estasi\Form\Interfaces
 */
interface FormAware
{
    /**
     * Returns an object that implements the interface \Estasi\Form\Interfaces\Form
     *
     * @return \Estasi\Form\Interfaces\Form
     */
    public function getForm(): Form;
}
