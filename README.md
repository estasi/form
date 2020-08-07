# Estasi From Plugin

Allows you to create chains of form fields, filter them, 
and validate them using plug-ins: `estasi/filter` and `estasi/validator`.

## Installation
To install with a composer:
```
composer require estasi/form
```

## Requirements
- PHP 7.4 or newer
- [Data Structures](https://github.com/php-ds/polyfill): 
    `composer require php-ds/php-ds`
    <br><small><i>Polyfill is installed with the estasi/form package.</i></small>

## Usage

The following field attributes are created automatically if the corresponding validators
 were set during class initialization (single, in a `Estasi\Validator\Chain`, or for `Estasi\Validator\Each`):
- name
- value (optional if the default value is set)
- required (optional, if the `\Estasi\Validator\Boolval` validator is set)
- pattern (optional, if the `\Estasi\Validator\Regex` validator is set)
- min (optional, if the `\Estasi\Validator\GreaterThan` validator is set)
- max (optional, if the `\Estasi\Validator\LessThan` validator is set)
- min and max (optional, if the `\Estasi\Validator\GreaterThan` and `\Estasi\Validator\LessThan` or
`\Estasi\Validator\Between` validators is set)
- minlength and maxlength (optional, if the `\Estasi\Validator\StringLength` validator is set)
- step (optional, if the `\Estasi\Validator\Step` validator is set)

### Basic usage
##### Create Form
```php
<?php
declare(strict_types=1);

use Estasi\Form\Field;
use Estasi\Form\Form;
use Estasi\Validator\Boolval;
use Estasi\Validator\Chain as ValidatorChain;
use Estasi\Validator\Email;
use Estasi\Validator\Identical;
use Estasi\Validator\Regex;


$loginValidator = (new ValidatorChain())->attach(new Boolval(Boolval::DISALLOW_STR_CONTAINS_ONLY_SPACE), ValidatorChain::WITH_BREAK_ON_FAILURE)
                                        ->attach(new Regex('[A-Za-z0-9_]{3,10}'), ValidatorChain::WITH_BREAK_ON_FAILURE);
$passwordValidator = (new ValidatorChain())->attach(new Boolval(Boolval::DISALLOW_STR_CONTAINS_ONLY_SPACE), ValidatorChain::WITH_BREAK_ON_FAILURE)
                                           ->attach(new Regex('((?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[~!?_@#$%^&+-]).{6,15})'), ValidatorChain::WITH_BREAK_ON_FAILURE);
$passwordConfirmValidator = $passwordValidator->attach(new Identical('password[confirm]', Identical::STRICT_IDENTITY_VERIFICATION));
$emailValidator = (new ValidatorChain())->attach(new Boolval(Boolval::DISALLOW_STR_CONTAINS_ONLY_SPACE), ValidatorChain::WITH_BREAK_ON_FAILURE)
                                        ->attach(new Email(Email::ALLOW_UNICODE), ValidatorChain::WITH_BREAK_ON_FAILURE);

$login = new Field('login', Field::WITHOUT_FILTER, $loginValidator, Field::WITH_BREAK_ON_FAILURE, Field::WITHOUT_DEFAULT_VALUE, 'Login', 'Tooltip Login');
$password = new Field('password[first]', Field::WITHOUT_FILTER, $passwordValidator, Field::WITH_BREAK_ON_FAILURE, Field::WITHOUT_DEFAULT_VALUE, 'Password', 'Tooltip Password');
$passwordConfirm = new Field('password[confirm]', Field::WITHOUT_FILTER, $passwordConfirmValidator, Field::WITH_BREAK_ON_FAILURE, Field::WITHOUT_DEFAULT_VALUE, 'Password Confirm', 'Tooltip Password');
$email = new Field('email', Field::WITHOUT_FILTER, $emailValidator, Field::WITH_BREAK_ON_FAILURE, Field::WITHOUT_DEFAULT_VALUE, 'Email', 'Tooltip Email');

$formReg = new Form($login, $password, $passwordConfirm, $email);
```

##### Using for template
For use in templates we recommend converting field data to an array.<br>
If the template engine allows, you can do without converting to an array and use Field objects to get all the same data via object methods.
```php
<?php
declare(strict_types=1);

/** @var \Estasi\Form\Form $formReg */
$fields = \Estasi\Form\Utility\Fields::convertToArray($formReg->getFields());
/*
$fields = [
    'login' => [
        'name' => 'login',
        'label' => 'Login',
        'tooltip' => 'Tooltip Login',
        'select' => null,
        'errors' => [],
        'attributes' => [
            [
                'name' => 'login',
                'required' => true,
                'pattern' => '[A-Za-z0-9_]{3,10}',
                'value'=> null,
            ],
        ],
        'required' => true,
    ],
    //...
];
*/
$fieldsJson = \Estasi\Form\Utility\Fields::convertToJson($formReg->getFields());
/*
$fieldsJson = '{
    "login": {
        "name": "login",
        "label": "Login",
        "tooltip": "Tooltip Login",
        "select": null,
        "errors": [],
        "attributes": [{"name":"login","required":true,"pattern":"[A-Za-z0-9_]{3,10}","value":null}],
        "required": true
    }
}';
*/
```
##### Use for checking data received from an html form
```php
<?php
declare(strict_types=1);

$requestedData = [
    'login' => 'Joe',
    'password[first]' => 'passwordJoe25!', 
    'password[confirm]' => 'passwordJoe25!', 
    'email' => 'joe@email.com'
];

/** @var \Estasi\Form\Form $formReg */
$formReg->setValues($requestedData);
if ($formReg->isValid()) {
    // We get all filtered and verified data of the same structure 
    // as the data passed for verification
    $validData = $formReg->getValues();
    // or Getting all valid Fields with the corresponding data
    $validFields = $formReg->getFieldsValid();
}
```
### Using an array of fields
It supports working with arrays of fields, such as "store_keywords[]".
The default value for these fields should be "array" or "null".
```php
<?php
declare(strict_types=1);

use Estasi\Filter\Callback;
use Estasi\Filter\Chain;
use Estasi\Filter\Each;
use Estasi\Form\Field;
use Estasi\Form\Form;
use Estasi\Form\Utility\Fields;
use Estasi\Validator\Boolval;

$defaultValues= [
    'store_keywords[]' => ['keyword 1', 'keyword 2'],
];

$filterEachKeyword = new Each(new Chain(Chain::DEFAULT_PLUGIN_MANAGER, 'trim', 'lowercase'));
$filterKeywords = (new Chain())->attach($filterEachKeyword)
                               ->attach(new Callback(fn(array $val): array => array_filter($val, 'boolval')));
$store_keywords = new Field('store_keywords[]', $filterKeywords, new Boolval(), Field::WITH_BREAK_ON_FAILURE, $defaultValues['store_keywords[]'], 'Keywords', 'Tooltip Keywords');
$form = new Form($store_keywords);

$fields = Fields::convertToArray($form->getFields());
/*
$fields = [
    'store_keywords[]' => [
        'name' => 'store_keywords[]',
        'label' => 'Keywords',
        'tooltip' => 'Tooltip Keywords',
        'select' => null,
        'errors' => [],
        'attributes' => [
            [
                'name' => 'store_keywords[]',
                'required' => true,
                'value'=> 'keyword 1',
            ],
            [
                'name' => 'store_keywords[]',
                'required' => true,
                'value'=> 'keyword 2',
            ],
        ],
        'required' => true,
    ],
];
*/
```

### Using with `<select>`

There is support for form fields of the `<select>` type. 
To do this when initializing the `Field` pass the `Select` object with the selection list.

If the default value matches one of the values in the list, the "selected" attribute is assigned to the value in the list.

```php
<?php
declare(strict_types=1);

use Estasi\Form\Field;
use Estasi\Form\Form;
use Estasi\Form\Option;
use Estasi\Form\Select;
use Estasi\Form\Utility\Fields;

// You can prepare the selection list by getting data from the database
/** @var \PDO $pdo */
$stm = $pdo->prepare(
    <<<SQL
    SELECT `name` `text`, JSON_OBJECT('value', `id`, 'title', `description`) `attributes`
    FROM `table`
    ORDER BY `id`;
    SQL
);
$stm->execute();
$optionsList = $stm->fetchAll(PDO::FETCH_CLASS, Option::class);

// or create a list manually
$optionsList = [
    new Option('Foo', ['value' => 1, 'title' => 'Foo it\'s ...']),
    new Option('Bar', ['value' => 2, 'title' => 'Bar great!']),
    new Option('Baz', ['value' => 3]),
];

$select = new Select(...$optionsList);
$type = new Field(
    'type', 
    Field::WITHOUT_FILTER, 
    Field::WITHOUT_VALIDATOR, 
    Field::WITH_BREAK_ON_FAILURE, 
    Field::WITHOUT_DEFAULT_VALUE, 
    'Type', 
    'Tooltip Type', 
    $select
);

$form = new Form($type);
$fields = Fields::convertToArray($form->getFields());
/*
$fields = [
    'type' => [
        'name' => 'type',
        'label' => 'Type',
        'tooltip' => 'Tooltip Type',
        'select' => [
            [
                'text' => 'Foo',
                'attributes' => ['title' => 'Foo it\'s ...', 'value' => 1]
            ],
            [
                'text' => 'Bar',
                'attributes' => ['title' => 'Bar great!', 'value' => 2]
            ],
            [
                'text' => 'Baz',
                'attributes' => ['value' => 3]
            ],
        ],
        'errors' => [],
        'attributes' => [
            [
                'name' => 'type',
                'value'=> null,
            ],
        ],
    ],
];
*/
```

## License
All contents of this package are licensed under the [BSD-3-Clause license](https://github.com/estasi/form/blob/master/LICENSE.md).
