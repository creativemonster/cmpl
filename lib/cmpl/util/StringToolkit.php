<?php
namespace cmpl\util;

class StringToolkit
{
    /**
     * Returns exploded last element
     *
     * @param  string $separator
     * @param  string $string
     * @return string|null
     */
    public static function getLastToken($separator, $string)
    {
        if (false === ($token = strrchr($string, $separator)))
        {
            return null;
        }

        if (false === ($token = substr($token, 1)))
        {
            return null;
        }

        return $token;
    }

    /**
     * Returns path from classname
     *
     * @param  string $classname
     * @param  string $separator
     * @return string
     */
    public static function classnameToPath($classname, $separator = '\\')
    {
        return str_replace($separator, DIRECTORY_SEPARATOR, $classname);
    }
}

