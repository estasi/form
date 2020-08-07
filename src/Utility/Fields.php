<?php

declare(strict_types=1);

namespace Estasi\Form\Utility;

use function json_decode;
use function json_encode;

use const JSON_BIGINT_AS_STRING;
use const JSON_INVALID_UTF8_IGNORE;
use const JSON_NUMERIC_CHECK;
use const JSON_PARTIAL_OUTPUT_ON_ERROR;
use const JSON_PRESERVE_ZERO_FRACTION;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_UNICODE;

/**
 * Class FieldsAsJson
 *
 * @package Estasi\Form\Utility
 * @deprecated
 */
final class Fields
{
    /**
     * Returns Fields data in JSON format
     *
     * @param iterable<\Estasi\Form\Interfaces\Field> $fields
     *
     * @return string json
     * @throws \JsonException
     * @noinspection PhpUndefinedClassInspection
     */
    public static function convertToJson(iterable $fields): string
    {
        return json_encode(
            $fields,
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_BIGINT_AS_STRING | JSON_PRESERVE_ZERO_FRACTION
            | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_NUMERIC_CHECK
        );
    }
    
    /**
     * Returns Fields data as an associative array
     *
     * @param iterable<\Estasi\Form\Interfaces\Field> $fields
     *
     * @return array
     * @throws \JsonException
     * @noinspection PhpUndefinedClassInspection
     */
    public static function convertToArray(iterable $fields): array
    {
        return json_decode(
            self::convertToJson($fields),
            true,
            512,
            JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING | JSON_INVALID_UTF8_IGNORE
        );
    }
}
