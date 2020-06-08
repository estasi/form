<?php

declare(strict_types=1);

namespace Estasi\Form\Factory;

use Estasi\Filter\{
    Chain as ChainFilter,
    Each as EachFilter,
    Interfaces\Filter
};
use Estasi\Form\Interfaces;
use Estasi\PluginManager\ReflectionPlugin;
use Estasi\Utility\Traits\ReceivedTypeForException;
use Estasi\Validator\{
    Chain as ChainValidator,
    Each as EachValidator,
    Interfaces\Validator
};
use InvalidArgumentException;

use function is_iterable;
use function sprintf;

/**
 * Class Field
 *
 * @package Estasi\Form\Factory
 */
final class Field
{
    use ReceivedTypeForException;

    /**
     * Returns the created Field object from the passed parameters
     *
     * @param iterable|\Estasi\Form\Interfaces\Field|\Estasi\Form\Interfaces\FieldAware|\Estasi\Form\Interfaces\FieldProvider $field
     *
     * @return \Estasi\Form\Interfaces\Field
     * @throws \ReflectionException
     * @throws \InvalidArgumentException
     */
    public function createField($field): Interfaces\Field
    {
        if ($field instanceof Interfaces\Field) {
            return $field;
        }

        if ($field instanceof Interfaces\FieldAware) {
            return $field->getField();
        }

        if ($field instanceof Interfaces\FieldProvider) {
            $field = $field->getSpecification();
        }

        if (is_iterable($field)) {
            $this->checkFilterInField($field[Interfaces\Field::OPT_FILTER]);
            $this->checkValidatorInField($field[Interfaces\Field::OPT_VALIDATOR]);

            /** @noinspection PhpIncompatibleReturnTypeInspection */
            return (new ReflectionPlugin(\Estasi\Form\Field::class))->newInstanceArgs($field);
        }

        throw new InvalidArgumentException(
            sprintf(
                'The specification for creating the Field was expected to be iterative; received %s!',
                $this->getReceivedType($field)
            )
        );
    }

    private function checkFilterInField(&$filter): void
    {
        if (Interfaces\Field::WITHOUT_FILTER === $filter || $filter instanceof Filter) {
            return;
        }
        if (false === is_iterable($filter)) {
            $filter = Interfaces\Field::WITHOUT_FILTER;

            return;
        }
        if (isset($filter[EachFilter::class])) {
            [EachFilter::OPT_FILTER => $filter, EachFilter::OPT_DELIMITER => $delimiter] = $filter[EachFilter::class];

            if (false === is_iterable($filter)) {
                $filter = [$filter];
            }

            $filter = new EachFilter(new ChainFilter(ChainFilter::DEFAULT_PLUGIN_MANAGER, ...$filter), $delimiter);

            return;
        }
        $filter = new ChainFilter(ChainFilter::DEFAULT_PLUGIN_MANAGER, ...$filter);
    }

    private function checkValidatorInField(&$validator): void
    {
        if (Interfaces\Field::WITHOUT_VALIDATOR === $validator || $validator instanceof Validator) {
            return;
        }
        if (false === is_iterable($validator)) {
            $validator = Interfaces\Field::WITHOUT_VALIDATOR;

            return;
        }
        if (isset($validator[EachValidator::class])) {
            [
                EachValidator::OPT_VALIDATOR => $validator,
                EachValidator::OPT_DELIMITER => $delimiter,
                EachValidator::OPT_OPTIONS   => $options,
            ] = $validator[EachValidator::class];

            if (false === is_iterable($validator)) {
                $validator = [$validator];
            }

            $validator = new EachValidator(
                new ChainValidator(ChainValidator::DEFAULT_PLUGIN_MANAGER, ...$validator),
                $delimiter,
                $options
            );

            return;
        }
        $validator = new ChainValidator(ChainValidator::DEFAULT_PLUGIN_MANAGER, ...$validator);
    }
}
