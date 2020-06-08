<?php

declare(strict_types=1);

namespace Estasi\Form\Traits;

use function preg_replace_callback;
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

        /** @noinspection RegExpRedundantEscape */
        return preg_replace_callback('`\[([\d\p{L}\x2D\x5F]+)\]`', fn(array $match): string => '.' . $match[1], $name);
    }
}
