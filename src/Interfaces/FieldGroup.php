<?php

declare(strict_types=1);

namespace Estasi\Form\Interfaces;

/**
 * Interface FieldGroup
 *
 * Provides a complex grouping of fields (array) United by the same name, but having different sub-names
 * For simple arrays of names, regular fields with the name ending in [] should be used, for example, example[]
 * Fields names[first], names[middle], names[last] you can combine it into a "names" group with a shared label and a
 * tooltip
 *
 * @package Estasi\Form\Interfaces
 */
interface FieldGroup extends Input
{
    /**
     * Returns filtered checked values of a group of fields
     *
     * @return iterable|null
     */
    public function getValue(): iterable|null;
    
    /**
     * Returns default values for the field group being checked
     *
     * @return iterable|null
     */
    public function getDefaultValue(): iterable|null;
    
    /**
     * Returns the original values of the field group being checked
     *
     * @return iterable|null
     */
    public function getRawValue(): iterable|null;
    
    /**
     * Setting fields
     *
     * The method MUST overwrite previously set fields
     *
     * @param \Estasi\Form\Interfaces\Field ...$fields
     */
    public function setFields(Field ...$fields): void;
    
    /**
     * Adding a new field
     *
     * The METHOD must return a new instance of the class
     *
     * @param \Estasi\Form\Interfaces\Field ...$fields
     *
     * @return $this new instance
     */
    public function withFields(Field ...$fields): self;
    
    /**
     * Returns all fields of the group
     *
     * @return iterable
     */
    public function getFields(): iterable;
    
    /**
     * FieldGroup constructor.
     *
     * @param string                        $name
     * @param string|null                   $label
     * @param string|null                   $tooltip
     * @param \Estasi\Form\Interfaces\Field ...$fields
     */
    public function __construct(
        string $name,
        ?string $label = self::WITHOUT_LABEL,
        ?string $tooltip = self::WITHOUT_TOOLTIP,
        Field ...$fields
    );
}
