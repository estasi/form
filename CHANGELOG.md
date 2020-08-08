# Changelog

## 2.0.0-dev

Compatible with php 8

### Added

- New interface `Estasi\Form\Interfaces\FieldGroup` and class `Estasy\Form\FieldGroup`
- New interface `Estasy\Form\Interfaces\Input` to combine interfaces `Estasi\Form\Interfaces\Field` and `Estasi\Form\Interfaces\FieldGroup`
- Union types

### Changed

- Order of parameters for the constructor of the `Estasy\Form\Field` class

### Deleted

- `Estasi\Form\Utility\Fields`

## 1.1.0
### Added

- `Utility\Fields::convertToJson(Fields[])` and `Utility\Fields::convertToArray(Fields[])`

### Fixed

- Fixed an error if the default value is empty when creating field attributes

### Deprecated

- `Estasi\Form\Utility\Fields`