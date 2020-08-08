<?php

declare(strict_types=1);

namespace Estasi\Form\Interfaces;

use JsonSerializable;

/**
 * Interface Input
 *
 * An interface that combines interfaces Fields and FieldsGroup and contains common methods
 *
 * @package Estasi\Form\Interfaces
 */
interface Input extends Verifiable, JsonSerializable
{
    // names of constructor parameters to create via the factory
    public const OPT_NAME             = 'name';
    public const OPT_BREAK_ON_FAILURE = 'breakOnFailure';
    public const OPT_DEFAULT_VALUE    = 'defaultValue';
    public const OPT_LABEL            = 'label';
    public const OPT_TOOLTIP          = 'tooltip';
    public const OPT_FIELDS           = 'fields';
    // values for constructor parameters
    public const WITH_BREAK_ON_FAILURE    = true;
    public const WITHOUT_BREAK_ON_FAILURE = false;
    public const WITHOUT_DEFAULT_VALUE    = null;
    public const WITHOUT_LABEL            = null;
    public const WITHOUT_TOOLTIP          = null;
    
    /**
     * Returns the name of the html form input
     *
     * @return string
     * @api
     */
    public function getName(): string;
    
    /**
     * @return bool
     */
    public function isBreakOnFailure(): bool;
    
    /**
     * Returns the filtered checked field value
     *
     * @return bool|float|int|iterable|string|null
     */
    public function getValue(): string|int|float|bool|iterable|null;
    
    /**
     * Returns the default value of the checked field
     *
     * @return bool|float|int|iterable|string|null
     */
    public function getDefaultValue(): string|int|float|bool|iterable|null;
    
    /**
     * Returns the original value of the checked field
     *
     * @return bool|float|int|iterable|string|null
     */
    public function getRawValue(): string|int|float|bool|iterable|null;
    
    /**
     * Returns a label for a form element
     *
     * @return string|null
     */
    public function getLabel(): ?string;
    
    /**
     * Returns the tooltip text of the field
     *
     * @return string|null
     */
    public function getTooltip(): ?string;
    
    /**
     * Setting a value for filtering and validation
     *
     * This method MUST be implemented in such a way as to preserve the immutability of the field and must return an
     * instance that has a verifiable value.
     *
     * @param iterable|null $value
     * @param iterable|null $context
     *
     * @return $this new instance
     */
    public function withValue(iterable|null $value, iterable|null $context = null): self;
    
    /**
     * FieldGroup constructor.
     *
     * @param string      $name    Name of a field or group of fields
     * @param string|null $label   Label of a field or group of fields
     * @param string|null $tooltip Tooltip for a field or group of fields
     */
    public function __construct(
        string $name,
        ?string $label = self::WITHOUT_LABEL,
        ?string $tooltip = self::WITHOUT_TOOLTIP
    );
}
