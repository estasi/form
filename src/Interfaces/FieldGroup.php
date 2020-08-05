<?php

declare(strict_types=1);

namespace Estasi\Form\Interfaces;

use JsonSerializable;

/**
 * Interface FieldGroup
 *
 * @package Estasi\Form\Interfaces
 */
interface FieldGroup extends Verifiable, JsonSerializable
{
    public function getName(): string;
    
    public function geLabel(): ?string;
    
    public function getTooltip(): ?string;
    
    public function withValue(iterable $value, $context = null): self;
    
    public function getValue();
    
    public function getDefaultValue();
    
    public function getRawValue();
    
    public function set(Field ...$fields): void;
    
    public function push(Field ...$fields): void;
    
    public function unshift(Field ...$fields): void;
    
    /**
     * FieldGroup constructor.
     *
     * @param string                        $name
     * @param string                        $label
     * @param string                        $tooltip
     * @param \Estasi\Form\Interfaces\Field ...$fields
     */
    public function __construct(string $name, string $label, string $tooltip, Field ...$fields);
}
