<?php

declare(strict_types=1);

namespace EstasiTest\Form;

use Estasi\Filter\Chain as FilterChain;
use Estasi\Form\Field;
use Estasi\Form\FieldGroup;
use Estasi\Form\Form;
use Estasi\Form\Option;
use Estasi\Utility\Json;
use Estasi\Validator\Boolval;
use Estasi\Validator\Chain;
use Estasi\Validator\Chain as ValidatorChain;
use Estasi\Validator\LessThan;
use Estasi\Validator\Regex;
use PHPUnit\Framework\TestCase;

/**
 * Class CreateFieldTest
 *
 * @package EstasiTest\Form
 */
class CreateFieldTest extends TestCase
{
    
    public function testException()
    {
        new Option();
        $this->expectException(\OutOfBoundsException::class);
    }
    
    public function testCreateField()
    {
        $tooltip = 'Length from 3 to 10 characters and can contain a number, uppercase'
                   . ' or lowercase Latin letter and a symbol _';
        $field   = new Field(
            'login',
            'Login',
            $tooltip,
            defaultValue: 'mikaxxl',
            validator: (new Chain())->attach(new Boolval())
                                    ->attach(new Regex('[a-zA-Z0-9]{2,100}')),
        );
        
        $this->assertInstanceOf(\Estasi\Form\Interfaces\Field::class, $field);
        
        \print_r(Json::encode($field, Json::DEFAULT_OPTIONS_ENCODE | \JSON_PRETTY_PRINT));
    }
    
    /**
     * @throws \JsonException
     * @noinspection PhpUndefinedClassInspection
     */
    public function testCreateFieldGroup()
    {
        $classifier  = Json::decode('{"1": 1, "2": 3, "3": 2, "4": 1, "5": 1, "6": 2, "7": 1}');
        $fieldsGroup = new FieldGroup(
            'classifications', 'Classifier', 'Tooltip classifier', $this->createFields($classifier)
        );
        
        echo Json::encode($fieldsGroup, Json::DEFAULT_OPTIONS_ENCODE | \JSON_PRETTY_PRINT);
        
        $this->assertTrue(true);
    }
    
    /**
     * @throws \JsonException
     * @noinspection PhpUndefinedClassInspection
     */
    public function testForm()
    {
        $data = [
            'name'            => 'полипропилен',
            'classifications' => [1 => 2, 3, 2, 1, 2, 1, 2],
        ];
        $form = new Form($this->getFieldName(), $this->getFieldClassifications());
        $form->setValues($data);
        
        $this->assertTrue($form->isValid());
        
        \print_r($form->getValues());
        \print_r($form->getLastErrors());
    }
    
    private function createFields(array $classifier)
    {
        $validator = new LessThan(2, true);
        $fields    = [];
        foreach ($classifier as $index => $value) {
            $fields[] = new Field(
                \sprintf('classifications[%d]', $index),
                \sprintf('Classification :: %d', $index),
                \sprintf('Tooltip :: %d', $index),
                ($index === 1),
                $value,
                null,
                $index === 1 ? $validator : null
            );
        }
        
        return $fields;
    }
    
    /**
     * @return \Estasi\Form\Field
     */
    private function getFieldName(): Field
    {
        return new Field(
            'name',
            'Название',
            'Length from 3 to 10 characters and can contain a number, uppercase or lowercase Latin letter and a symbol _',
            true,
            null,
            new FilterChain(FilterChain::DEFAULT_PLUGIN_MANAGER, 'trim', 'uppercase'),
            (new ValidatorChain())->attach(new Boolval(Boolval::DISALLOW_STR_CONTAINS_ONLY_SPACE))
                                  ->attach(new Regex('[а-яА-я0-9\s_]{2,100}', Regex::OFFSET_ZERO))
        );
    }
    
    /**
     * @return \Estasi\Form\FieldGroup
     * @throws \JsonException
     * @noinspection PhpUndefinedClassInspection
     */
    private function getFieldClassifications(): FieldGroup
    {
        $classifier = Json::decode('{"1": 1, "2": 3, "3": 2, "4": 1, "5": 1, "6": 2, "7": 1}');
        
        return new FieldGroup(
            'classifications', 'Classifier', 'Tooltip classifier', ...$this->createFields($classifier)
        );
    }
}
