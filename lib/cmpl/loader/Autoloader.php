<?php
namespace cmpl\loader;

class Autoloader
{
    private $_namespaces = array();

    public function __construct()
    {
        $this->registerNamespace('cmpl', realpath(__DIR__ . '/../..'));
    }

    public function registerNamespace($ns, $path)
    {
        $this->_namespaces[$ns] = rtrim($path, '/');
        return $this;
    }

    public function getNamespaces()
    {
        return $this->_namespaces;
    }

    public function getPath($ns)
    {
        if (!isset($this->_namespaces[$ns]))
        {
            throw new \InvalidArgumentException(sprintf('Namespace "%s" not found', $ns));
        }

        return $this->_namespaces[$ns];
    }

    public function loadClass($class)
    {
        if (class_exists($class, false) || interface_exists($class, false))
        {
            return true;
        }

        $class = trim($class, '\\');

        if (false === ($pos = strpos($class, '\\')))
        {
            return false;
        }

        $key = substr($class, 0, $pos);

        if (!isset($this->_namespaces[$key]))
        {
            return false;
        }

        $pathname = sprintf('%s/%s.php', $this->_namespaces[$key], str_replace('\\', DIRECTORY_SEPARATOR, $class));

        if (file_exists($pathname))
        {
            require $pathname;

            if (class_exists($class, false) || interface_exists($class, false))
            {
                return true;
            }
        }
    }

    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }
}

