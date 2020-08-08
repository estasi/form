<?php

declare(strict_types=1);

namespace Estasi\Form\Interfaces;

use Estasi\Filter\Interfaces\Filter;
use Estasi\Validator\Interfaces\Validator;

/**
 * Interface Field
 *
 * @package Estasi\Form\Interfaces
 */
interface Field extends Input
{
    // names of constructor parameters to create via the factory
    public const OPT_FILTER    = 'filter';
    public const OPT_VALIDATOR = 'validator';
    public const OPT_SELECT    = 'select';
    // values for constructor parameters
    public const WITHOUT_FILTER    = null;
    public const WITHOUT_VALIDATOR = null;
    public const WITHOUT_SELECT    = null;
    
    /**
     * Setting a value for filtering and validation
     *
     * This method MUST be implemented in such a way as to preserve the immutability of the field and must return an
     * instance that has a verifiable value.
     *
     * @param string|int|float|bool|iterable|null $value
     * @param string|int|float|bool|iterable|null $context
     *
     * @return $this new instance
     */
    public function withValue(
        string|int|float|bool|iterable|null $value,
        string|int|float|bool|iterable|null $context = null
    ): self;
    
    /**
     * Returns drop-down list data
     *
     * @return \Estasi\Form\Interfaces\Select|null
     */
    public function getSelect(): ?Select;
    
    /**
     * Returns a list of attributes for the field
     * It is created automatically from the provided data
     * List of attributes to return:
     * - name
     * - value (optional if the default value is set)
     * - required (optional, if the \Estasi\Validator\Boolval validator is set)
     * - pattern (optional, if the \Estasi\Validator\Regex validator is set)
     * - min (optional, if the \Estasi\Validator\GreaterThan validator is set)
     * - max (optional, if the \Estasi\Validator\LessThan validator is set)
     * - min and max (optional, if the \Estasi\Validator\GreaterThan and \Estasi\Validator\LessThan or
     * \Estasi\Validator\Between validators is set)
     * - minlength and maxlength (optional, if the \Estasi\Validator\StringLength validator is set)
     * - step (optional, if the \Estasi\Validator\Step validator is set)
     *
     * @return iterable<iterable<string, string|int|float|bool>>
     */
    public function getAttributes(): iterable;
    
    /**
     * Field constructor.
     *
     * @param string                                      $name           Field Name (attribute "name")
     * @param bool                                        $breakOnFailure Whether to stop checking the other fields if
     *                                                                    an error occurs in checking the data for the
     *                                                                    current field
     * @param string|null                                 $label          Field label - text of the label tag
     * @param string|null                                 $tooltip        A tooltip (for example, a popup) to the field
     * @param string|int|float|bool|iterable|null         $defaultValue   Default field value (attribute "value" input
     *                                                                    or textarea text)
     * @param \Estasi\Filter\Interfaces\Filter|null       $filter         Filter or filter chain applied to the field
     *                                                                    value
     * @param \Estasi\Validator\Interfaces\Validator|null $validator      Validator or chain of validators applied to
     *                                                                    the field value
     * @param \Estasi\Form\Interfaces\Select|null         $select         Data from the select drop - down list.
     *                                                                    Without markup (formatting).
     */
    public function __construct(
        string $name,
        ?string $label = self::WITHOUT_LABEL,
        ?string $tooltip = self::WITHOUT_TOOLTIP,
        bool $breakOnFailure = self::WITHOUT_BREAK_ON_FAILURE,
        string|int|float|bool|iterable|null $defaultValue = self::WITHOUT_DEFAULT_VALUE,
        ?Filter $filter = self::WITHOUT_FILTER,
        ?Validator $validator = self::WITHOUT_VALIDATOR,
        ?Select $select = self::WITHOUT_SELECT
    );
}
