<?php

declare(strict_types=1);

namespace Estasi\Form\Interfaces;

use JsonSerializable;

/**
 * Interface OptGroup
 *
 * @package Estasi\Form\Interfaces
 */
interface OptGroup extends JsonSerializable
{
    // names of constructor parameters to create via the factory
    public const OPT_LABEL      = 'label';
    public const OPT_DISABLED   = 'disabled';
    public const OPT_ATTRIBUTES = 'attributes';
    // default values for constructor parameters
    public const DISABLED           = true;
    public const ENABLE             = false;
    public const WITHOUT_ATTRIBUTES = null;

    /**
     * Returns a list of attributes for the optgroup tag (including the "label" and "disabled" attributes)
     *
     * @return iterable<string, string|int|float|bool>
     */
    public function getAttributes(): iterable;

    /**
     * Returns a list of \Estasi\Form\Interfaces\Option objects
     *
     * @return \Estasi\Form\Interfaces\Option[]
     */
    public function getOptions(): iterable;

    /**
     * OptGroup constructor.
     *
     * @param string                                              $label      Text that will be included in the list as
     *                                                                        the group header.
     * @param bool                                                $disabled   Blocks access to the list group.
     * @param string|iterable<string, string|int|float|bool>|null $attributes Generic attributes and events
     * @param \Estasi\Form\Interfaces\Option                      ...$options Option tag data as an object
     */
    public function __construct(
        string $label,
        bool $disabled = self::ENABLE,
        $attributes = self::WITHOUT_ATTRIBUTES,
        Option ...$options
    );
}
