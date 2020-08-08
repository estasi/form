<?php

declare(strict_types=1);

namespace Estasi\Form\Factory;

use Estasi\Filter\Chain as ChainFilter;
use Estasi\Filter\Each as EachFilter;
use Estasi\Filter\Interfaces\Filter;
use Estasi\Form\Interfaces;
use Estasi\PluginManager\ReflectionPlugin;
use Estasi\Utility\Traits\ReceivedTypeForException;
use Estasi\Validator\Chain as ChainValidator;
use Estasi\Validator\Each as EachValidator;
use Estasi\Validator\Interfaces\Validator;

use function is_iterable;

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
     * @param \Estasi\Form\Interfaces\Field|\Estasi\Form\Interfaces\FieldAware|\Estasi\Form\Interfaces\FieldGroup|\Estasi\Form\Interfaces\FieldProvider|iterable $field
     *
     * @return \Estasi\Form\Interfaces\Field
     * @throws \ReflectionException
     */
    public function createField(
        iterable|Interfaces\Field|Interfaces\FieldGroup|Interfaces\FieldProvider|Interfaces\FieldAware $field
    ): Interfaces\Field {
        if ($field instanceof Interfaces\Field || $field instanceof Interfaces\FieldGroup) {
            return $field;
        }
        
        if ($field instanceof Interfaces\FieldAware) {
            return $field->getField();
        }
        
        if ($field instanceof Interfaces\FieldProvider) {
            $field = $field->getSpecification();
        }
        
        $this->checkFilterInField($field[Interfaces\Field::OPT_FILTER]);
        $this->checkValidatorInField($field[Interfaces\Field::OPT_VALIDATOR]);
        
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return (new ReflectionPlugin(\Estasi\Form\Field::class))->newInstanceArgs($field);
    }
    
    private function checkFilterInField(iterable|Filter|null &$filter): void
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
