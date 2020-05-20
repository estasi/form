<?php

declare(strict_types=1);

namespace Estasi\Form\Interfaces;

use Estasi\Filter\Interfaces\Filter;
use Estasi\Validator\Interfaces\Validator;
use JsonSerializable;

/**
 * Interface Field
 *
 * @package Estasi\Form\Interfaces
 */
interface Field extends Verifiable, JsonSerializable
{
    // names of constructor parameters to create via the factory
    public const OPT_NAME             = 'name';
    public const OPT_FILTER           = 'filter';
    public const OPT_VALIDATOR        = 'validator';
    public const OPT_BREAK_ON_FAILURE = 'breakOnFailure';
    public const OPT_DEFAULT_VALUE    = 'defaultValue';
    public const OPT_LABEL            = 'label';
    public const OPT_TOOLTIP          = 'tooltip';
    public const OPT_SELECT           = 'select';
    // values for constructor parameters
    public const WITHOUT_FILTER           = null;
    public const WITHOUT_VALIDATOR        = null;
    public const WITH_BREAK_ON_FAILURE    = true;
    public const WITHOUT_BREAK_ON_FAILURE = false;
    public const WITHOUT_DEFAULT_VALUE    = null;
    public const WITHOUT_LABEL            = null;
    public const WITHOUT_TOOLTIP          = null;
    public const WITHOUT_SELECT           = null;

    /**
     * Returns the name of the html form input
     *
     * @return string
     * @api
     */
    public function getName(): string;

    /**
     * Setting a value for filtering and validation
     *
     * This method MUST be implemented in such a way as to preserve the immutability of the field and must return an
     * instance that has a verifiable value.
     *
     * @param mixed      $value
     * @param mixed|null $context
     *
     * @return $this new instance
     */
    public function withValue($value, $context = null): self;

    /**
     * Returns the filtered checked field value
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Returns the default value of the checked field
     *
     * @return mixed
     */
    public function getDefaultValue();

    /**
     * Returns the original value of the checked field
     *
     * @return mixed
     */
    public function getRawValue();

    /**
     * @return bool
     */
    public function isBreakOnFailure(): bool;

    /**
     * Returns the tooltip text of the field
     *
     * @return string|null
     */
    public function getTooltip(): ?string;

    /**
     * Returns a label for a form element
     *
     * @return string|null
     */
    public function getLabel(): ?string;

    /**
     * Returns drop-down list data
     *
     * @return \Estasi\Form\Interfaces\Select
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
     * @return iterable<string, string|int|float|bool>
     */
    public function getAttributes(): iterable;

    /**
     * Field constructor.
     *
     * @param string                                      $name           Field Name (attribute "name")
     * @param \Estasi\Filter\Interfaces\Filter|null       $filter         Filter or filter chain applied to the field
     *                                                                    value
     * @param \Estasi\Validator\Interfaces\Validator|null $validator      Validator or chain of validators applied to
     *                                                                    the field value
     * @param bool                                        $breakOnFailure Whether to stop checking the other fields if
     *                                                                    an error occurs in checking the data for the
     *                                                                    current field
     * @param string|int|float|bool|null                  $defaultValue   Default field value (attribute "value" input
     *                                                                    or textarea text)
     * @param string|null                                 $label          Field label - text of the label tag
     * @param string|null                                 $tooltip        A tooltip (for example, a popup) to the field
     * @param \Estasi\Form\Interfaces\Select|null         $select         Data from the select drop - down list.
     *                                                                    Without markup (formatting).
     */
    public function __construct(
        string $name,
        ?Filter $filter = self::WITHOUT_FILTER,
        ?Validator $validator = self::WITHOUT_VALIDATOR,
        bool $breakOnFailure = self::WITHOUT_BREAK_ON_FAILURE,
        $defaultValue = self::WITHOUT_DEFAULT_VALUE,
        ?string $label = self::WITHOUT_LABEL,
        ?string $tooltip = self::WITHOUT_TOOLTIP,
        ?Select $select = self::WITHOUT_SELECT
    );
}
