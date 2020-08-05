<?php

declare(strict_types=1);

namespace Estasi\Form\Interfaces;

use JsonSerializable;

/**
 * Interface Option
 *
 * @property-read string      $label
 * @property-read string      $value
 * @property-read string|null $title
 * @package Estasi\Form\Interfaces
 */
interface Option extends JsonSerializable
{
    // names of constructor parameters to create via the factory
    public const OPT_LABEL      = 'label';
    public const OPT_VALUE      = 'value';
    public const OPT_TITLE      = 'title';
    public const OPT_ATTRIBUTES = 'attributes';
    // default values for constructor parameters
    public const WITHOUT_TITLE      = null;
    public const WITHOUT_ATTRIBUTES = null;
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
     * @return string
     */
    public function getValue(): string;
    
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
     * @return iterable<string, string|int|float|bool>
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
     * @param string                                       $label      Selection text
     * @param string                                       $value      The value of the selection
     * @param string|null                                  $title      Brief description of the selection
     * @param iterable<string, string|int|float|bool>|null $attributes Attributes of the option tag
     */
    public function __construct(
        string $label,
        string $value,
        ?string $title = self::WITHOUT_TITLE,
        ?iterable $attributes = self::WITHOUT_ATTRIBUTES
    );
}
