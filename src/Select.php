<?php

declare(strict_types=1);

namespace Estasi\Form;

use Ds\Vector;
use IteratorAggregate;

/**
 * Class Select
 *
 * @package Estasi\Form
 */
final class Select implements Interfaces\Select, IteratorAggregate
{
    private Vector $options;

    /**
     * @inheritDoc
     */
    public function __construct(...$options)
    {
        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $this->options = (new Vector($options))->filter(
            fn($option): bool => ($option instanceof Interfaces\Option || $option instanceof Interfaces\OptGroup)
        );
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
    public function count()
    {
        return $this->options->count();
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->options;
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return $this->options;
    }
}
