<?php
namespace cmpl\util;

class ArrayToolkit
{
    /**
     * @param  array $array
     * @param  array $keys
     * @return array
     */
    public static function convertKey(array $array, array $keys)
    {
        foreach ($keys as $from => $to)
        {
            if (array_key_exists($from, $array))
            {
                $array[$to] = $array[$from];
                unset($array[$from]);
            }
        }

        return $array;
    }

    /**
     * @param  array $array1
     * @param  array $array2
     * @return array
     */
    public static function deepMerge(array $array1, array $array2)
    {
        $args     = func_get_args();
        $argPos   = 0;
        $newArray = array_shift($args);

        foreach ($args as $array)
        {
            ++$argPos;

            if (!is_array($array))
            {
                throw new \InvalidArgumentException(sprintf('Argument %d must be an array', $argPos));
            }

            foreach ($array as $key => $value)
            {
                if (is_array($value) && array_key_exists($key, $newArray) && is_array($newArray[$key]))
                {
                    $newArray[$key] = static::deepMerge($newArray[$key], $value);
                }
                else
                {
                    $newArray[$key] = $value;
                }
            }
        }

        return $newArray;
    }

    /**
     * @param  array $array
     * @return array
     */
    public static function flatten(array $array)
    {
        foreach ($array as $key => $value)
        {
            if (is_array($value))
            {
                unset($array[$key]);
                $array = array_merge($array, $value);
            }
        }

        return $array;
    }

    /**
     * @param  array $array
     * @return array
     */
    public static function deepFlatten(array $array)
    {
        foreach ($array as $key => $value)
        {
            if (is_array($value))
            {
                unset($array[$key]);
                $array = array_merge($array, static::deepFlatten($value));
            }
        }

        return $array;
    }
}

