<?php

declare(strict_types=1);

namespace Estasi\Form;

use Ds\Map;
use OutOfBoundsException;

/**
 * Class Option
 *
 * @package Estasi\Form
 */
final class Option implements Interfaces\Option
{
    use Traits\AssertName;
    use Traits\GetAttributesAsIterable;
    
    private ?string                    $label;
    private null|int|bool|float|string $value;
    private ?string                    $title;
    private Map|string|iterable        $attributes;
    private int                        $include;
    
    /**
     * @inheritDoc
     * @throws \JsonException
     * @noinspection PhpUndefinedClassInspection
     */
    public function __construct(
        string|null $label = null,
        string|int|float|bool|null $value = null,
        string|null $title = null,
        iterable|null $attributes = null
    ) {
        if (isset($label)) {
            $this->label = $label;
        }
        if (isset($value)) {
            $this->value = $value;
        }
        if (isset($title)) {
            $this->title = $title;
        }
        
        $errorMessage = 'Label (text) option is not specified!';
        isset($this->label)
            ? $this->assertName($this->label, $errorMessage)
            : (throw new OutOfBoundsException($errorMessage));
        if (false === isset($this->value)) {
            throw new OutOfBoundsException('Value option is not specified!');
        }
        
        if (isset($attributes)) {
            $this->attributes = $attributes;
        }
        $this->attributes = isset($this->attributes)
            ? new Map($this->getAttributesAsIterable($this->attributes))
            : new Map();
        
        $this->include = self::INCLUDE_ALL;
    }
    
    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            self::OPT_LABEL      => $this->label,
            self::OPT_VALUE      => $this->value,
            self::OPT_TITLE      => $this->title,
            self::OPT_ATTRIBUTES => $this->getAttributes($this->include),
        ];
    }
    
    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return $this->label;
    }
    
    /**
     * @inheritDoc
     */
    public function getValue(): string|int|float|bool|null
    {
        return $this->value;
    }
    
    /**
     * @inheritDoc
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }
    
    /**
     * @inheritDoc
     */
    public function setIncludeAttributesJsonSerialize(int $include): void
    {
        $this->include = $include;
    }
    
    /**
     * @inheritDoc
     */
    public function getAttributes(int $include = self::INCLUDE_ALL): iterable
    {
        $attributes = $this->attributes->copy();
        if (($include & self::INCLUDE_TITLE) === self::INCLUDE_TITLE) {
            $attributes->put(self::OPT_TITLE, $this->title);
        }
        if (($include & self::INCLUDE_VALUE) === self::INCLUDE_VALUE) {
            $attributes->put(self::OPT_VALUE, $this->value);
        }
        
        return $attributes;
    }
}
