<?php

if (!function_exists('str_or_null')) {
    /**
     * function str_or_null
     *
     * @param mixed $value
     *
     * @return ?string
     */
    function str_or_null(mixed $value): ?string
    {
        return filter_var($value, FILTER_DEFAULT, FILTER_NULL_ON_FAILURE);
    }
}
