<?php

declare(strict_types=1);

namespace Estasi\Form\Interfaces;

/**
 * Interface Form
 *
 * A container containing all the form fields that has a single point for setting values for filtering and checking and
 * a single point for getting them after filtering and checking
 *
 * @package Estasi\Form\Interfaces
 */
interface Form extends Verifiable
{
    /**
     * Returns true if the name field is found in the form elements, or false if it is not found
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasField(string $name): bool;

    /**
     * Returns an object of the \Estasi\Form\Interfaces\Field class by its name (without value)
     *
     * @param string $name
     *
     * @return \Estasi\Form\Interfaces\Field
     * @throws \OutOfBoundsException If the "name" field is not found in the form elements!
     */
    public function getField(string $name): Field;

    /**
     * Returns a list of \Estasi\Form\Interfaces\Field objects (without values)
     *
     * @return iterable<\Estasi\Form\Interfaces\Field>
     */
    public function getFields(): iterable;

    /**
     * Returns a list of checked \Estasi\Form\Interfaces\Field objects (with values)
     *
     * @return iterable<\Estasi\Form\Interfaces\Field>
     */
    public function getFieldsValid(): iterable;

    /**
     * Returns a list of \Estasi\Form\Interfaces\Field objects that failed validation (with values and errors)
     *
     * @return iterable<\Estasi\Form\Interfaces\Field>
     */
    public function getFieldsInvalid(): iterable;

    /**
     * Sets the value of the form to filter and validate
     *
     * @param iterable $values
     *
     * @api
     */
    public function setValues(iterable $values): void;

    /**
     * Returns form values after filtering and checking (in the same structure as they were received in)
     *
     * @return iterable
     * @api
     */
    public function getValues(): iterable;

    /**
     * Form constructor.
     *
     * @param \Estasi\Form\Interfaces\Field ...$fields Form field
     */
    public function __construct(Field ...$fields);
}
