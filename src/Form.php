<?php

declare(strict_types=1);

namespace Estasi\Form;

use Ds\Map;
use Estasi\Form\Interfaces\Field;
use Estasi\Utility\{
    ArrayUtils,
    Traits\Errors
};
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

    /** @var \Ds\Map|Field[] */
    private Map $fields;

    /** @var \Ds\Map|Field[] */
    private Map $fieldsValid;

    /** @var \Ds\Map|Field[] */
    private Map $fieldsInvalid;

    private array $values;

    /**
     * @inheritDoc
     */
    public function __construct(Field ...$fields)
    {
        $this->fields = new Map();
        foreach ($fields as $field) {
            $this->fields->put($field->getName(), $field);
        }
        $this->fieldsValid   = new Map();
        $this->fieldsInvalid = new Map();
        $this->values        = [];
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
    public function getField(string $name): Field
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
    public function setValues(iterable $values): void
    {
        $this->values = ArrayUtils::iteratorToArray($values);
    }

    /**
     * @inheritDoc
     */
    public function getValues(): iterable
    {
        return $this->values;
    }

    /**
     * @inheritDoc
     */
    public function isValid(): bool
    {
        $isValid = true;
        $values  = [];

        foreach ($this->fields as $name => $field) {
            $name  = $this->squareBracketsToDotDelimiter($field->getName());
            $field = $field->withValue(ArrayUtils::get($name, $this->values), $this->values);

            if ($field->isValid()) {
                $values[$name] = $field->getValue();
                $this->fieldsValid->put($name, $field);
                continue;
            }
    
            $isValid = false;
            $this->fieldsInvalid->put($name, $field);
            $this->mergeErrors($field->getLastErrors());
    
            if ($field->isBreakOnFailure()) {
                break;
            }
        }
        $this->values = ArrayUtils::oneToMultiDimArray($values);

        return $isValid;
    }
}
