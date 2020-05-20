<?php

declare(strict_types=1);

namespace Estasi\Form;

use Ds\Map;

/**
 * Class Option
 *
 * @package Estasi\Form
 */
final class Option implements Interfaces\Option
{
    use Traits\GetAttributesAsIterable;

    private string   $text;
    private Map      $attributes;

    /**
     * @inheritDoc
     */
    public function __construct(?string $text, $attributes = null)
    {
        $this->text       = $text ?? '';
        $this->attributes = new Map($this->getAttributesAsIterable($attributes));
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
