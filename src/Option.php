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

    private string $text;
    /** @var \Ds\Map|string|null */
    private $attributes;

    /**
     * @inheritDoc
     */
    public function __construct(?string $text = null, $attributes = null)
    {
        if (false === isset($this->text)) {
            $this->text = $text ?? '';
        }
        if (isset($this->attributes)) {
            $attributes = $this->attributes;
        }
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
