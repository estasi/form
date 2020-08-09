<?php

declare(strict_types=1);

namespace Estasi\Form;

use Ds\Map;
use Estasi\Form\Interfaces\Field;
use Estasi\Form\Interfaces\FieldGroup;
use Estasi\Utility\ArrayUtils;
use Estasi\Utility\Traits\Errors;
use Generator;
use OutOfBoundsException;

use function sprintf;

/**
 * Class Form
 *
 * @package Estasi\Form
 */
final class Form implements Interfaces\Form
{
    use Errors;
    use Traits\Validation;
    use Traits\SquareBracketsToDot;
    
    /** @var \Ds\Map<string, \Estasi\Form\Interfaces\Field|\Estasi\Form\Interfaces\FieldGroup> */
    private Map    $fields;
    /** @var \Ds\Map<string, \Estasi\Form\Interfaces\Field|\Estasi\Form\Interfaces\FieldGroup> */
    private Map    $fieldsValid;
    /** @var \Ds\Map<string, \Estasi\Form\Interfaces\Field|\Estasi\Form\Interfaces\FieldGroup> */
    private Map    $fieldsInvalid;
    private array  $rawValues;
    private ?array $values;
    
    /**
     * @inheritDoc
     */
    public function __construct(Field|FieldGroup ...$fields)
    {
        $this->fields = new Map();
        foreach ($fields as $field) {
            $this->fields->put($field->getName(), $field);
        }
        $this->fieldsValid   = new Map();
        $this->fieldsInvalid = new Map();
        $this->rawValues     = [];
        $this->values        = null;
    }
    
    /**
     * @inheritDoc
     */
    public function hasField(string $name): bool
    {
        return $this->fields->hasKey($name);
    }
    
    /**
     * @inheritDoc
     */
    public function getField(string $name): Field|FieldGroup
    {
        try {
            return $this->fields->get($name);
        } catch (OutOfBoundsException $exception) {
            throw new OutOfBoundsException(sprintf('The "%s" field was not found in the form elements!', $name));
        }
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
    public function getFieldsValid(): iterable
    {
        return $this->fieldsValid->copy();
    }
    
    /**
     * @inheritDoc
     */
    public function getFieldsInvalid(): iterable
    {
        return $this->fieldsInvalid->copy();
    }
    
    /**
     * @inheritDoc
     */
    public function getValues(): ?iterable
    {
        return $this->values;
    }
    
    /**
     * @inheritDoc
     */
    public function setValues(iterable $values): void
    {
        $this->rawValues = ArrayUtils::iteratorToArrayRecursive($values);
    }
    
    /**
     * @inheritDoc
     */
    public function isValid(): bool
    {
        $isValid = true;
        /**
         * @var string                                                           $name
         * @var \Estasi\Form\Interfaces\Field|\Estasi\Form\Interfaces\FieldGroup $field
         */
        foreach ($this->genFields() as $name => $field) {
            if ($field->isValid()) {
                $this->values[$name] = $field->getValue();
                $this->fieldsValid->put($field->getName(), $field);
                continue;
            }
        
            $isValid = false;
            $this->fieldsInvalid->put($field->getName(), $field);
            $this->mergeErrors($field->getLastErrors());
        
            if ($field->isBreakOnFailure()) {
                break;
            }
        }
    
        $this->values = ArrayUtils::oneToMultiDimArray($this->values);
    
        return $isValid;
    }
    
    private function genFields(): Generator
    {
        /**
         * @var string                                                           $name
         * @var \Estasi\Form\Interfaces\Field|\Estasi\Form\Interfaces\FieldGroup $field
         */
        foreach ($this->fields as $name => $field) {
            $name  = $this->squareBracketsToDotDelimiter($name);
            $value = ArrayUtils::get($name, $this->rawValues);
            $field = $field instanceof FieldGroup
                ? $field->withValue(ArrayUtils::oneToMultiDimArray([$name => $value]), $this->rawValues)
                : $field->withValue($value, $this->rawValues);
            
            yield $name => $field;
        }
    }
}
