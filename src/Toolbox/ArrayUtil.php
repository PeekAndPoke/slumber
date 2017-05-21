<?php

namespace PeekAndPoke\Component\Toolbox;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class ArrayUtil
{
    /**
     * @param array  $array
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public static function get($array, $key, $default = null)
    {
        $array = self::ensureArray($array);

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        return $default;
    }

    /**
     * @param array  $src
     * @param string $chainKey f.e. "root.sub.item"
     * @param null   $default
     * @param string $separator
     *
     * @return mixed
     */
    public static function getNested($src, $chainKey, $default = null, $separator = '.')
    {
        if (empty($src) || empty($chainKey) || empty($separator)) {
            return $default;
        }

        $parts = explode($separator, (string) $chainKey);

        foreach ($parts as $part) {
            if (! isset($src[$part])) {
                return $default;
            }

            $src = $src[$part];
        }

        return $src;
    }

    /**
     * @param $value
     *
     * @return array
     */
    public static function ensureArray($value)
    {
        if ($value === null) {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        return [$value];
    }
}
