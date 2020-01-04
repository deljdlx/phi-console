<?php

namespace Phi\Console;


class Option
{

    private $name;

    private $alias = [];

    private $defaultValue;

    private $value;

    public function __construct($name, $defaultValue = null)
    {
        $this->name = $name;
        $this->defaultValue = $defaultValue;
    }


    public function getName()
    {
        return $this->name;
    }

    public function addAlias($alias)
    {
        $this->alias[] = $alias;
    }

    public function getValue()
    {
        if ($this->value !== null) {
            return $this->value;
        }
        else {
            if ($this->defaultValue !== null) {
                return $this->defaultValue;
            }
        }

        $aliasBuffer = '';
        foreach ($this->alias as $alias) {
            $aliasBuffer .= "\t" . $alias . "\n";
        }

        throw new Exception('Option ' . $this->name . ' is mandatory.' . "\n" . 'Arguments : ' . "\n" . $aliasBuffer);
    }


    /**
     * @param $alias
     * @return bool
     */
    public function hasAlias($alias)
    {

        if (array_search($alias, $this->alias) !== false) {
            return true;
        }
        return false;
    }


    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }


}
