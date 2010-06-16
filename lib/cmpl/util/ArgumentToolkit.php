<?php
namespace cmpl\util;

use ReflectionFunction;
use ReflectionMethod;


class ArgumentToolkit
{
    /**
     * @param  mixed             $callback
     * @param  array|ArrayAccess $parameters
     * @return array
     */
    public static function decorate($callback, $parameters = array())
    {
        if (!is_callable($callback))
        {
            throw new \InvalidArgumentException('Not callable');
        }

        if (!is_array($parameters) && !$parameters instanceof \ArrayAccess)
        {
            throw new \InvalidArgumentException('Parameters must be an array or an object ArrayAccess');
        }

        $ref = null;

        if (is_string($callback))
        {
            $ref = new ReflectionFunction($callback);
        }
        else if (is_array($callback) && 1 < count($callback))
        {
            $ref = new ReflectionMethod($callback[0], $callback[1]);
        }
        else if (is_object($callback))
        {
            $ref = new ReflectionMethod($callback, '__invoke');
        }

        if (!$ref instanceof \ReflectionFunctionAbstract)
        {
            throw new \InvalidArgumentException('Not callable');
        }

        $args = array();

        /* @var $parameter ReflectionParameter */
        foreach ($ref->getParameters() as $parameter)
        {
            $name = $parameter->getName();

            if (!isset($parameters[$name]) && !$parameter->isOptional())
            {
                throw new \InvalidArgumentException(sprintf('Argument "%s" not found', $name));
            }

            if (isset($parameters[$name]))
            {
                $args[] = $parameters[$name];
            }
            else if ($ref->isInternal())
            {
                break;
            }
            else
            {
                $args[] = $parameter->getDefaultValue();
            }
        }

        return $args;
    }

    /**
     * @param  mixed             $callback
     * @param  array|ArrayAccess $parameters
     * @return mixed
     */
    public static function call($callback, $parameters = array())
    {
        return call_user_func_array($callback, static::decorate($callback, $parameters));
    }
}