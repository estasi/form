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
    
    private string     $text;
    private Map|string $attributes;
    
    /**
     * @inheritDoc
     */
    public function __construct(?string $text = null, string|iterable|null $attributes = null)
    {
        if (isset($text)) {
            $this->text = $text;
        }
        
        $errorMessage = 'Text option is not specified!';
        isset($this->text)
            ? $this->assertName($this->text, $errorMessage)
            : (throw new OutOfBoundsException($errorMessage));
        
        if (isset($attributes)) {
            $this->attributes = $attributes;
        }
        $this->attributes = isset($this->attributes)
            ? new Map($this->getAttributesAsIterable($this->attributes))
            : new Map();
    }
    
    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return $this->text;
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
    public function jsonSerialize()
    {
        return [self::OPT_TEXT => $this->text, self::OPT_ATTRIBUTES => $this->attributes];
    }
}
