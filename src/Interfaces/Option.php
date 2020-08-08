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
    public const OPT_TEXT       = 'text';
    public const OPT_ATTRIBUTES = 'attributes';
    
    /**
     * Returns text of the option tag
     *
     * @return string
     */
    public function getText(): string;
    
    /**
     * Returns a list of attributes for the option tag
     *
     * @return iterable<string, string|int|float|bool>
     */
    public function getAttributes(): iterable;
    
    /**
     * Option constructor.
     *
     * @param string|null                                         $text       Text of the option tag
     * @param string|iterable<string, string|int|float|bool>|null $attributes Attributes of the option tag, including
     *                                                                        value
     *
     * @throws \JsonException
     * @noinspection PhpUndefinedClassInspection
     */
    public function __construct(?string $text, string|iterable|null $attributes = null);
}
