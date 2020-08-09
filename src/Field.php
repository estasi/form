<?php

declare(strict_types=1);

namespace Estasi\Form;

use Ds\Map;
use Ds\Vector;
use Estasi\Filter\Interfaces\Filter;
use Estasi\Utility\Traits\Errors;
use Estasi\Utility\Traits\ReceivedTypeForException;
use Estasi\Validator\Between;
use Estasi\Validator\Boolval;
use Estasi\Validator\Each;
use Estasi\Validator\GreaterThan;
use Estasi\Validator\Interfaces\Chain as ValidatorChain;
use Estasi\Validator\Interfaces\Validator;
use Estasi\Validator\LessThan;
use Estasi\Validator\Regex;
use Estasi\Validator\Step;
use Estasi\Validator\StringLength;
use Generator;
use InvalidArgumentException;

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
    use Traits\AssertName;
    use Traits\Validation;
    
    private array                               $values;
    private Vector                              $attributes;
    private string|int|float|bool|iterable|null $context;
    
    /**
     * @inheritDoc
     */
    public function __construct(
        private string $name,
        private ?string $label = self::WITHOUT_LABEL,
        private ?string $tooltip = self::WITHOUT_TOOLTIP,
        private bool $breakOnFailure = self::WITHOUT_BREAK_ON_FAILURE,
        private string|int|float|bool|iterable|null $defaultValue = self::WITHOUT_DEFAULT_VALUE,
        private ?Filter $filter = self::WITHOUT_FILTER,
        private ?Validator $validator = self::WITHOUT_VALIDATOR,
        private ?Interfaces\Select $select = self::WITHOUT_SELECT
    ) {
        $this->assertName($this->name);
        $this->assertDefaultValue($this->name, $this->defaultValue);
        
        $this->values     = ['value' => null, 'raw' => null];
        $this->attributes = $this->createAttributes();
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
    public function withValue(
        string|int|float|bool|iterable|null $value,
        string|int|float|bool|iterable|null $context = null
    ): Interfaces\Field {
        $field                  = clone $this;
        $field->values['raw']   = $value;
        $field->values['value'] = $this->filter ? ($this->filter)($value) : $value;
        $field->context         = $context;
        
        return $field;
    }
    
    /**
     * @inheritDoc
     */
    public function getValue(): string|int|float|bool|iterable|null
    {
        return $this->values['value'];
    }
    
    /**
     * @inheritDoc
     */
    public function getDefaultValue(): string|int|float|bool|iterable|null
    {
        return $this->defaultValue;
    }
    
    /**
     * @inheritDoc
     */
    public function getRawValue(): string|int|float|bool|iterable|null
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
            $defaultValue ??= [];
    
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
        
            if ($validator instanceof ValidatorChain) {
                foreach ($this->genValidators($validator) as $validatorInChain) {
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
    
    private function genValidators(ValidatorChain $chain): Generator
    {
        foreach ($chain->getValidators() as [$validator]) {
            yield $validator;
        }
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
