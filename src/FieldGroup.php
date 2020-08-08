<?php

declare(strict_types=1);

namespace Estasi\Form;

use Ds\Vector;
use Estasi\Form\Interfaces\Field;
use Estasi\Utility\ArrayUtils;
use Estasi\Utility\Traits\Errors;
use Generator;

use function str_replace;

/**
 * Class FieldGroup
 *
 * @package Estasi\Form
 */
final class FieldGroup implements Interfaces\FieldGroup
{
    use Errors;
    use Traits\AssertName;
    use Traits\Validation;
    use Traits\SquareBracketsToDot;
    
    private bool $breakOnFailure;
    /** @var \Estasi\Form\Interfaces\Field[] */
    private array|Vector $fields;
    
    /**
     * @inheritDoc
     */
    public function __construct(
        private string $name,
        private ?string $label = self::WITHOUT_LABEL,
        private ?string $tooltip = self::WITHOUT_TOOLTIP,
        Field ...$fields
    ) {
        $this->assertName($name);
        $this->setFields(...$fields);
        $this->breakOnFailure = false;
    }
    
    /**
     * @inheritDoc
     */
    public function getValue(): iterable|null
    {
        return $this->getValues(__FUNCTION__);
    }
    
    /**
     * @inheritDoc
     */
    public function getDefaultValue(): iterable|null
    {
        return $this->getValues(__FUNCTION__);
    }
    
    /**
     * @inheritDoc
     */
    public function getRawValue(): iterable|null
    {
        return $this->getValues(__FUNCTION__);
    }
    
    /**
     * @inheritDoc
     */
    public function withFields(Field ...$fields): Interfaces\FieldGroup
    {
        $new         = clone $this;
        $new->fields = $this->fields->copy();
        $new->fields->push(...$fields);
        
        return $new;
    }
    
    /**
     * @inheritDoc
     */
    public function getFields(): iterable
    {
        return $this->fields->copy();
    }
    
    /**
     * @inheritDoc
     */
    public function setFields(Field ...$fields): void
    {
        $this->fields = new Vector($fields);
    }
    
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * @inheritDoc
     */
    public function isBreakOnFailure(): bool
    {
        return $this->breakOnFailure;
    }
    
    /**
     * @inheritDoc
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }
    
    /**
     * @inheritDoc
     */
    public function getTooltip(): ?string
    {
        return $this->tooltip;
    }
    
    /**
     * @inheritDoc
     */
    public function withValue(?iterable $value, ?iterable $context = null): Interfaces\Input
    {
        $new = clone $this;
        if ($value) {
            $new->fields = $this->fields->map(
                fn(Field $field): Field => $field->withValue(
                    ArrayUtils::get($this->squareBracketsToDotDelimiter($field->getName()), $value, null),
                    $context
                )
            );
        }
        
        return $new;
    }
    
    /**
     * @inheritDoc
     */
    public function isValid(): bool
    {
        $isValid = true;
        foreach ($this->genFields() as $field) {
            if ($field->isValid()) {
                continue;
            }
            $isValid = false;
            $this->mergeErrors($field->getLastErrors());
            if ($this->breakOnFailure = $field->isBreakOnFailure()) {
                break;
            }
        }
        
        return $isValid;
    }
    
    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            self::OPT_NAME    => $this->name,
            self::OPT_LABEL   => $this->label,
            self::OPT_TOOLTIP => $this->tooltip,
            self::OPT_FIELDS  => $this->fields,
            'errors'          => $this->getLastErrors(),
        ];
    }
    
    /**
     * @param string $method
     *
     * @return array
     */
    private function getValues(string $method): array
    {
        $values = [];
        foreach ($this->genFields() as $field) {
            $name          = str_replace($this->name, '', $field->getName());
            $name          = $this->squareBracketsToDotDelimiter($name);
            $values[$name] = $field->{$method}();
        }
        
        return ArrayUtils::oneToMultiDimArray($values);
    }
    
    private function genFields(): Generator
    {
        foreach ($this->fields as $field) {
            yield $field;
        }
    }
}
