<?php

declare(strict_types=1);

namespace Estasi\Form\Interfaces;

use JsonSerializable;

/**
 * Interface Option
 *
 * @package Estasi\Form\Interfaces
 */
interface Option extends JsonSerializable
{
    // names of constructor parameters to create via the factory
    public const OPT_LABEL      = 'label';
    public const OPT_VALUE      = 'value';
    public const OPT_TITLE      = 'title';
    public const OPT_ATTRIBUTES = 'attributes';
    // flags used by getAttributes method
    public const INCLUDE_NONE  = 0;
    public const INCLUDE_VALUE = 1 << 0;
    public const INCLUDE_TITLE = 1 << 1;
    public const INCLUDE_ALL   = (1 << 2) - 1;
    
    /**
     * Returns text of the option tag
     *
     * @return string
     */
    public function getLabel(): string;
    
    /**
     * Returns the value of selection
     *
     * @return bool|float|int|string|null
     */
    public function getValue(): string|int|float|bool|null;
    
    /**
     * Returns brief description of the selection
     *
     * @return string|null
     */
    public function getTitle(): ?string;
    
    /**
     * Returns a list of attributes for the option tag
     *
     * The returned value MUST NOT contain label
     * The returned value can include value and title defined by special flags
     *
     * @param int $include
     *
     * @return iterable<string, string|int|float|bool|null>
     */
    public function getAttributes(int $include = self::INCLUDE_ALL): iterable;
    
    /**
     * Sets whether the value and title are included in attributes during serialization
     *
     * @param int $include
     */
    public function setIncludeAttributesJsonSerialize(int $include): void;
    
    /**
     * Option constructor.
     *
     * @param string|null                                  $label      Selection text
     * @param bool|float|int|string|null                   $value      The value of the selection
     * @param string|null                                  $title      Brief description of the selection
     * @param iterable<string, string|int|float|bool>|null $attributes Attributes of the option tag
     */
    public function __construct(
        string|null $label = null,
        string|int|float|bool|null $value = null,
        string|null $title = null,
        iterable|null $attributes = null
    );
}
