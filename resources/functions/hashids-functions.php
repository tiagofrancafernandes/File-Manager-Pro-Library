<?php

use Hashids\Hashids;

if (!function_exists('hashids')) {
    /**
     * function hashids
     *
     * @param string $salt
     * @param ?int $minHashLength
     * @param string $alphabet
     *
     * @return Hashids
     */
    function hashids(
        string $salt = '',
        ?int $minHashLength = null,
        string $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
    ): Hashids {
        $minHashLength = !$minHashLength || $minHashLength < 3 ? 15 : $minHashLength;

        /**
         * @var Hashids $hashids
         */
        return new Hashids(
            $salt,
            $minHashLength,
            $alphabet,
        );
    }
}

if (!function_exists('hashids_encode_hex')) {
    /**
     * function hashids_encode_hex
     *
     * @param string $str
     *
     * @return string
     */
    function hashids_encode_hex(string $str): string
    {
        return hashids()->encodeHex($str);
    }
}

if (!function_exists('hashids_decode_hex')) {
    /**
     * function hashids_decode_hex
     *
     * @param string $str
     *
     * @return string
     */
    function hashids_decode_hex(string $str): string
    {
        return hashids()->decodeHex($str);
    }
}

if (!function_exists('hashids_encode')) {
    /**
     * function hashids_encode
     *
     * @param ...$numbers
     *
     * @return string
     */
    function hashids_encode(...$numbers): string
    {
        return hashids()->encode(...$numbers);
    }
}

if (!function_exists('hashids_decode')) {
    /**
     * function hashids_decode
     *
     * @param string $str
     *
     * @return array
     */
    function hashids_decode(string $str): array
    {
        return hashids()->decode($str);
    }
}
