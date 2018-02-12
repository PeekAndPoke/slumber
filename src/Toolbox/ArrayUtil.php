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
     * @param array  $array
     * @param string $path e.g. "root.sub.item"
     * @param mixed  $default
     * @param string $separator
     *
     * @return mixed
     */
    public static function getNested($array, $path, $default = null, $separator = '.')
    {
        if (empty($array) ||
            empty($path) ||
            empty($separator)) {

            return $default;
        }

        $parts = explode($separator, (string) $path);

        foreach ($parts as $part) {
            if (! isset($array[$part])) {
                return $default;
            }

            $array = $array[$part];
        }

        return $array;
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

        if (\is_array($value) ||
            $value instanceof \ArrayObject
        ) {
            return (array) $value;
        }

        if ($value instanceof \Traversable) {
            $result = [];

            foreach ($value as $k => $v) {
                $result[$k] = $v;
            }

            return $result;
        }

        return [$value];
    }

    /**
     * @param $arr
     *
     * @return bool
     */
    public static function isAssoc($arr)
    {
        if (! \is_array($arr)) {
            return false;
        }

        return array_keys($arr) !== range(0, \count($arr) - 1);
    }

    /**
     * Removes all empty values recursively
     *
     * @param mixed $input
     *
     * @return mixed
     */
    public static function clean($input)
    {
        if (\is_array($input)) {

            $output = [];

            /** @var array $input */
            foreach ($input as $k => $v) {

                $cleaned = self::clean($v);

                if ($cleaned !== null || (\is_array($cleaned) && \count($cleaned) === 0)) {
                    $output[$k] = $cleaned;
                }
            }

            return $output;
        }

        if (\is_object($input)) {

            $output = new \stdClass();

            /** @var array $input */
            foreach ((array) $input as $k => $v) {

                $cleaned = self::clean($v);

                if ($cleaned !== null || (\is_array($cleaned) && \count($cleaned) === 0)) {
                    $output->{$k} = $cleaned;
                }
            }

            return $output;
        }

        return $input;
    }
}
