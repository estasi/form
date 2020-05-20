<?php

declare(strict_types=1);

namespace Estasi\Form\Traits;

/**
 * Trait Validation
 *
 * @package Estasi\Form\Traits
 */
trait Validation
{
    /**
     * @inheritDoc
     */
    public function notValid(): bool
    {
        return false === $this->isValid();
    }

    /**
     * @inheritDoc
     */
    public function __invoke(): bool
    {
        return $this->isValid();
    }
}
