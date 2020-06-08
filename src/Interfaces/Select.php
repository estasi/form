<?php

declare(strict_types=1);

namespace Estasi\Form\Interfaces;

use Countable;
use JsonSerializable;
use Traversable;

/**
 * Interface OptionsList
 *
 * @package Estasi\Form\Interfaces
 */
interface Select extends Traversable, Countable, JsonSerializable
{
    /**
     * Returns a list of \Estasi\Form\Interfaces\Option or \Estasi\Form\Interfaces\OptGroup objects
     *
     * @return \Estasi\Form\Interfaces\Option[]|\Estasi\Form\Interfaces\OptGroup[]
     */
    public function getOptions(): iterable;

    /**
     * OptionsList constructor.
     *
     * @param \Estasi\Form\Interfaces\Option|\Estasi\Form\Interfaces\OptGroup ...$options
     */
    public function __construct(...$options);
}
