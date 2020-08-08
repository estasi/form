<?php

declare(strict_types=1);

namespace Estasi\Form\Factory;

use Ds\Vector;
use Estasi\Form\Interfaces;
use Estasi\Utility\Traits\ReceivedTypeForException;

/**
 * Class Form
 *
 * @package Estasi\Form\Factory
 */
final class Form
{
    use ReceivedTypeForException;
    
    /**
     * Returns the created Form object from the passed parameters
     *
     * @param iterable|\Estasi\Form\Interfaces\Form|\Estasi\Form\Interfaces\FormAware|\Estasi\Form\Interfaces\FormProvider $form
     *
     * @return \Estasi\Form\Interfaces\Form
     */
    public function createForm(
        iterable|Interfaces\Form|Interfaces\FormAware|Interfaces\FormProvider $form
    ): Interfaces\Form {
        if ($form instanceof Interfaces\Form) {
            return $form;
        }
        
        if ($form instanceof Interfaces\FormAware) {
            return $form->getForm();
        }
        
        if ($form instanceof Interfaces\FormProvider) {
            $form = $form->getSpecification();
        }
        
        return new \Estasi\Form\Form(...(new Vector($form))->map([new Field(), 'createField']));
    }
}
