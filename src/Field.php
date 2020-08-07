<?php

declare(strict_types=1);

namespace Estasi\Form;

use Ds\Map;
use Ds\Vector;
use Estasi\Filter\Interfaces\Filter;
use Estasi\Utility\{
    Traits\Errors,
    Traits\ReceivedTypeForException
};
use Estasi\Validator\{
    Between,
    Boolval,
    Each,
    GreaterThan,
    Interfaces\Chain as ChainValidator,
    Interfaces\Validator,
    LessThan,
    Regex,
    Step,
    StringLength
};
use InvalidArgumentException;
use OutOfBoundsException;

use function array_merge;
use function compact;
use function is_iterable;
use function sprintf;
use function substr_compare;

/**
 * Class Field
 *
 * @package Estasi\Form
 */
final class Field implements Interfaces\Field
{
    use ReceivedTypeForException;
    use Errors;
    use Traits\Validation;

    private string             $name;
    private ?Filter            $filter;
    private ?Validator         $validator;
    private bool               $breakOnFailure;
    private ?string            $label;
    private ?string            $tooltip;
    private ?Interfaces\Select $select;
    private array              $values;
    /** @var \Ds\Vector|\Ds\Map[] */
    private Vector $attributes;
    /** @var mixed */
    private $context;

    /**
     * @inheritDoc
     */
    public function __construct(
        string $name,
        ?Filter $filter = self::WITHOUT_FILTER,
        ?Validator $validator = self::WITHOUT_VALIDATOR,
        bool $breakOnFailure = self::WITHOUT_BREAK_ON_FAILURE,
        $defaultValue = self::WITHOUT_DEFAULT_VALUE,
        ?string $label = self::WITHOUT_LABEL,
        ?string $tooltip = self::WITHOUT_TOOLTIP,
        ?Interfaces\Select $select = self::WITHOUT_SELECT
    ) {
        $this->assertName($name);
        $this->assertDefaultValue($name, $defaultValue);

        $this->name           = $name;
        $this->filter         = $filter;
        $this->validator      = $validator;
        $this->breakOnFailure = $breakOnFailure;
        $this->label          = $label;
        $this->tooltip        = $tooltip;
        $this->select         = $select;
        $this->values         = ['value' => null, 'raw' => null, 'default' => $defaultValue];
        $this->attributes     = $this->createAttributes();
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
    public function withValue($value, $context = null): Interfaces\Field
    {
        $field                  = clone $this;
        $field->values['raw']   = $value;
        $field->values['value'] = $this->filter ? ($this->filter)($value) : $value;
        $field->context         = $context;

        return $field;
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->values['value'];
    }

    /**
     * @inheritDoc
     */
    public function getDefaultValue()
    {
        return $this->values['default'];
    }

    /**
     * @inheritDoc
     */
    public function getRawValue()
    {
        return $this->values['raw'];
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
    public function getTooltip(): ?string
    {
        return $this->tooltip;
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
    public function getSelect(): ?Interfaces\Select
    {
        return $this->select;
    }

    /**
     * @inheritDoc
     */
    public function getAttributes(): iterable
    {
        return $this->attributes;
    }

    /**
     * @inheritDoc
     */
    public function isValid(): bool
    {
        if (isset($this->validator) && false === $this->validator->isValid($this->getValue(), $this->context)) {
            $this->setErrors($this->validator->getLastErrors());

            return false;
        }

        return true;
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
            self::OPT_SELECT  => $this->select,
            'errors'          => $this->getLastErrors(),
            'attributes'      => $this->attributes,
            'required'        => $this->attributes->get(0)
                                                  ->get('required', false),
        ];
    }

    public function __clone()
    {
        if (isset($this->filter)) {
            $this->filter = clone $this->filter;
        }
        if (isset($this->validator)) {
            $this->validator = clone $this->validator;
        }
    }

    /**
     * Throws an exception if the name is an empty string (a string consisting only of space characters is considered
     * an empty string)
     *
     * @param string $name
     *
     * @throws \OutOfBoundsException
     */
    private function assertName(string $name): void
    {
        $boolval = new Boolval(Boolval::DISALLOW_STR_CONTAINS_ONLY_SPACE);
        if (false === $boolval($name)) {
            throw new OutOfBoundsException('The specified field name is empty!');
        }
    }

    /**
     * Throws an exception if the field name is an array, but the passed default value is not
     *
     * @param string $name
     * @param mixed  $defaultValue
     *
     * @throws \InvalidArgumentException
     */
    private function assertDefaultValue(string $name, &$defaultValue): void
    {
        if ($this->isFieldArray($name)) {
            if (self::WITHOUT_DEFAULT_VALUE === $defaultValue) {
                $defaultValue = [];

                return;
            }
            if (false === is_iterable($defaultValue)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'For the array field "...[]", the default value must be iterable; received "%s"!',
                        $this->getReceivedType($defaultValue)
                    )
                );
            }
        }
    }

    private function createAttributes(): Vector
    {
        $attributes = new Vector();
        $attrs      = new Map([self::OPT_NAME => $this->name, 'required' => false]);

        if (isset($this->validator)) {
            $validator = $this->validator instanceof Each ? $this->validator->getValidator() : $this->validator;

            if ($validator instanceof ChainValidator) {
                foreach ($validator->getValidators() as [$validatorInChain]) {
                    $attrs->putAll($this->getAttributeByValidator($validatorInChain));
                }
            } else {
                $attrs->putAll($this->getAttributeByValidator($validator));
            }
        }

        if ($this->isFieldArray($this->name)) {
            $defaultValues = new Vector($this->getDefaultValue());
            if ($defaultValues->isEmpty()) {
                $defaultValues->push(null);
            }
            $defaultValues->map(fn($value) => $attributes->push($attrs->merge(compact('value'))));
        } else {
            $attributes->push($attrs->merge(['value' => $this->getDefaultValue()]));
        }

        return $attributes;
    }

    private function getAttributeByValidator(Validator $validator): array
    {
        if ($validator instanceof Boolval) {
            return ['required' => true];
        }
        if ($validator instanceof Regex) {
            return ['pattern' => $validator->pattern['html']];
        }
        if ($validator instanceof GreaterThan) {
            return ['min' => $validator->min];
        }
        if ($validator instanceof LessThan) {
            return ['max' => $validator->max];
        }
        if ($validator instanceof Between) {
            return ['min' => $validator->min, 'max' => $validator->max];
        }
        if ($validator instanceof StringLength) {
            $maxlength = $validator->max > $validator::NO_LENGTH_LIMITATION ? ['maxlength' => $validator->max] : [];

            return array_merge(['minlength' => $validator->min], $maxlength);
        }
        if ($validator instanceof Step) {
            return ['step' => $validator->step];
        }

        return [];
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    private function isFieldArray(string $name): bool
    {
        return 0 === substr_compare($name, '[]', -2);
    }
}
