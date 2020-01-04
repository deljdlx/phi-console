<?php

namespace Phi\Console;


class Command
{

    private $commandLine;
    private $arguments;

    private $argv;
    private $argc;

    private $usedIndexes = [];


    /**
     * @var CommandFilter[]
     */
    private $filters = [];


    /**
     * @var Option[]
     */
    private $options = [];

    public function __construct($argv = null, $argc = null)
    {

        if ($argv === null) {
            global $argv;
        }

        if ($argc === null) {
            global $argc;
        }

        $this->argv = $argv;
        $this->argc = $argc;
        $this->commandLine = implode(' ', $this->argv);
    }


    public function execute()
    {
        $this->extractArguments();
        $this->executeFilters();
        return $this;
    }


    public function extractArguments()
    {
        foreach ($this->argv as $index => $value) {

            if (isset($this->usedIndexes[$index])) {
                continue;
            }

            if (strpos($value, '-') === 0) {
                $this->usedIndexes[$index] = true;
                $argumentName = $value;

                $this->extractArgumentValue($argumentName, $index, $value);
            }
        }

        foreach ($this->argv as $index => $value) {
            if (!isset($this->usedIndexes[$index]) && $index > 0) {
                $this->arguments[$value] = $value;
                $this->usedIndexes[$index] = true;
            }
        }

        foreach ($this->arguments as $alias => $value) {
            if($option = $this->getOptionByAlias($alias)) {
                $option->setValue($value);
            }
        }
    }


    /**
     * @param $alias
     * @return bool|Option
     */
    public function getOptionByAlias($alias)
    {
        foreach ($this->options as $option) {
            if($option->hasAlias($alias)) {
                return $option;
            }
        }
        return false;
    }


    public function getOptionValue($optionName)
    {
        if($option = $this->getOptionByName($optionName)) {
            return $option->getValue();
        }
        return null;
    }

    /**
     * @param $name
     * @return bool|Option
     */
    public function getOptionByName($name)
    {
        if(isset($this->options[$name])) {
            return $this->options[$name];
        }
        return false;
    }




    /**
     * @param Option $option
     * @return $this
     */
    public function addOption(Option $option)
    {
        $this->options[$option->getName()] = $option;
        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }


    public function addFilter(CommandFilter $filter, $name = null)
    {
        if($name === null) {
            $this->filters[] = $filter;
        }
        else {
            $this->filters[$name] = $filter;
        }

        return $this;
    }


    public function executeFilters()
    {
        foreach ($this->filters as $name => $filter) {
            $filter->execute($this);
        }

        return $this;
    }



    /**
     * @param $argumentName
     * @param $index
     * @param $value
     * @return $this
     */
    private function extractArgumentValue($argumentName, $index, $value)
    {
        if (strpos($value, '=') === false) {
            if (isset($this->argv[$index + 1])) {
                $value = $this->argv[$index + 1];
                if (strpos($value, '-') === 0) {
                    $value = true;
                }
                else {
                    $this->usedIndexes[$index + 1] = true;
                }
            }
            else {
                $value = true;
            }
        }
        else {
            $parts = explode('=', $value);
            $value = $parts[1];
            $argumentName = $parts[0];
        }

        $this->arguments[$argumentName] = $value;

        return $this;
    }


}