<?php

declare(strict_types=1);

namespace Estasi\Form\Interfaces;

use Estasi\Utility\Interfaces\Errors;

/**
 * Interface Verifiable
 *
 * @package Estasi\Form\Interfaces
 */
interface Verifiable extends Errors
{
    /**
     * Returns TRUE if value meets the validation requirements,
     * if value does not pass validation, this method returns FALSE.
     *
     * @return bool
     * @api
     */
    public function isValid(): bool;

    /**
     * Returns TRUE if value does not meet validation requirements,
     * if value passes validation, this method returns FALSE.
     *
     * @return bool
     * @api
     */
    public function notValid(): bool;

    /**
     * Returns TRUE if value meets the validation requirements,
     * if value does not pass validation, this method returns FALSE.
     *
     * synonym isValid()
     *
     * @return bool
     * @api
     */
    public function __invoke(): bool;
}
