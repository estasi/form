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
     * Returns an object of the \Estasi\Form\Interfaces\Input class by its name (without value)
     *
     * @param string $name
     *
     * @return \Estasi\Form\Interfaces\Field|\Estasi\Form\Interfaces\FieldGroup
     * @throws \OutOfBoundsException If the "name" field is not found in the form elements!
     */
    public function getField(string $name): Field|FieldGroup;
    
    /**
     * Returns a list of \Estasi\Form\Interfaces\Input objects (without values)
     *
     * @return \Estasi\Form\Interfaces\Field[]|\Estasi\Form\Interfaces\FieldGroup[]|iterable
     */
    public function getFields(): iterable;
    
    /**
     * Returns a list of checked \Estasi\Form\Interfaces\Input objects (with values)
     *
     * @return \Estasi\Form\Interfaces\Field[]|\Estasi\Form\Interfaces\FieldGroup[]|iterable
     */
    public function getFieldsValid(): iterable;
    
    /**
     * Returns a list of \Estasi\Form\Interfaces\Input objects that failed validation (with values and errors)
     *
     * @return \Estasi\Form\Interfaces\Field[]|\Estasi\Form\Interfaces\FieldGroup[]|iterable
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
     * It is created only after calling the isValid() or not Valid () methods
     * It MUST return null before calling one of these methods
     *
     * @return iterable|null
     * @api
     */
    public function getValues(): ?iterable;
    
    /**
     * Form constructor.
     *
     * @param \Estasi\Form\Interfaces\Field|\Estasi\Form\Interfaces\FieldGroup ...$fields
     */
    public function __construct(Field|FieldGroup ...$fields);
}
