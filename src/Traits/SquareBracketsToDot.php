<?php

declare(strict_types=1);

namespace Estasi\Form\Traits;

use function preg_replace_callback_array;
use function substr;
use function substr_compare;

/**
 * Trait SquareBracketsToDot
 *
 * @package Estasi\Form\Traits
 */
trait SquareBracketsToDot
{
    private function squareBracketsToDotDelimiter(string $name): string
    {
        _loop_:
        if (0 === substr_compare($name, '[]', -2)) {
            $name = substr($name, 0, -2);
            goto _loop_;
        }
    
        return preg_replace_callback_array(
            [
                '`\[([\d\p{L}\x2D\x5F]+)\]`' => fn(array $match): string => '.' . $match[1],
                '`^\x2E{1}`'                 => fn(array $match) => '',
            ],
            $name
        );
    }
}
