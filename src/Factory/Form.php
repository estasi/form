<?php

declare(strict_types=1);

namespace Estasi\Form\Factory;

use Ds\Vector;
use Estasi\Form\Interfaces;
use Estasi\Utility\Traits\ReceivedTypeForException;
use InvalidArgumentException;

use function is_iterable;

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
    public function createForm($form): Interfaces\Form
    {
        if ($form instanceof Interfaces\Form) {
            return $form;
        }

        if ($form instanceof Interfaces\FormAware) {
            return $form->getForm();
        }

        if ($form instanceof Interfaces\FormProvider) {
            $form = $form->getSpecification();
        }

        if (is_iterable($form)) {
            return new \Estasi\Form\Form(...(new Vector($form))->map([new Field(), 'createField']));
        }

        throw new InvalidArgumentException(
            sprintf(
                'The specification for creating the Form was expected to be iterative; received %s!',
                $this->getReceivedType($form)
            )
        );
    }
}
