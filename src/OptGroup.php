<?php

declare(strict_types=1);

namespace Estasi\Form;

use Ds\Map;
use Ds\Vector;
use Estasi\Form\Interfaces\Option;

/**
 * Class OptGroup
 *
 * @package Estasi\Form
 */
final class OptGroup implements Interfaces\OptGroup
{
    use Traits\GetAttributesAsIterable;

    private Map    $attributes;
    private Vector $options;

    /**
     * @inheritDoc
     */
    public function __construct(
        string $label,
        bool $disabled = self::ENABLE,
        $attributes = self::WITHOUT_ATTRIBUTES,
        Option ...$options
    ) {
        $this->attributes = new Map([self::OPT_LABEL => $label, self::OPT_DISABLED => $disabled]);
        /** @noinspection PhpParamsInspection */
        $this->attributes->putAll($this->getAttributesAsIterable($attributes));
        $this->options = new Vector($options);
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
    public function getOptions(): iterable
    {
        return $this->options;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [self::OPT_ATTRIBUTES => $this->attributes, 'options' => $this->options];
    }
}
