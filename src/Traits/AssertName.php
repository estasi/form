<?php

declare(strict_types=1);

namespace Estasi\Form\Traits;

use Estasi\Validator\Boolval;
use OutOfBoundsException;

/**
 * Trait AssertNameField
 *
 * @package Estasi\Form\Traits
 */
trait AssertName
{
    /**
     * Throws an exception if the name is an empty string (a string consisting only of space characters is considered
     * an empty string)
     *
     * @param string|null $name
     *
     * @param string|null $message
     *
     * @throws \OutOfBoundsException
     */
    protected function assertName(?string $name, ?string $message = null): void
    {
        if (false === (new Boolval(Boolval::DISALLOW_STR_CONTAINS_ONLY_SPACE))($name)) {
            throw new OutOfBoundsException($message ?? 'The specified field name is empty!');
        }
    }
}
