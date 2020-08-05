<?php

declare(strict_types=1);

namespace Estasi\Form;

use Ds\Map;
use Estasi\Utility\Traits\Properties__get;

use function compact;

/**
 * Class Option
 *
 * @package Estasi\Form
 */
final class Option implements Interfaces\Option
{
    use Traits\GetAttributesAsIterable;
    use Properties__get;
    
    private Map $attributes;
    private int $include;
    
    /**
     * @inheritDoc
     */
    public function __construct(
        string $label,
        string $value,
        ?string $title = self::WITHOUT_TITLE,
        ?iterable $attributes = self::WITHOUT_ATTRIBUTES
    ) {
        $this->setProperties(compact('label', 'value', 'title'));
        $this->attributes = new Map($attributes ?? []);
        $this->include    = self::INCLUDE_ALL;
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
    public function getValue(): string
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
    
    public function setIncludeAttributesJsonSerialize(int $include): void
    {
        $this->include = $include;
    }
    
    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->properties->merge([self::OPT_ATTRIBUTES => $this->getAttributes($this->include)]);
    }
}
