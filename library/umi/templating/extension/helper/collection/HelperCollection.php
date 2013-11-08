<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\extension\helper\collection;

use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\templating\exception\InvalidArgumentException;
use umi\templating\exception\OutOfBoundsException;
use umi\templating\exception\RuntimeException;
use umi\templating\extension\helper\IHelperFactoryAware;
use umi\templating\extension\helper\THelperFactoryAware;

/**
 * Коллекция помощников шаблонов.
 */
class HelperCollection implements IHelperCollection, IHelperFactoryAware, ILocalizable
{
    use THelperFactoryAware;
    use TLocalizable;

    /**
     * @var array $helpers зарегистрированные помощники для шаблонов
     */
    protected $helpers = [];

    /**
     * {@inheritdoc}
     */
    public function addHelpers(array $helpers)
    {
        foreach ($helpers as $name => $helper) {
            $this->addHelper($name, $helper);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addHelper($name, $helper)
    {
        if (isset($this->helpers[$name])) {
            throw new RuntimeException($this->translate(
                'Helper "{name}" already registered.',
                ['name' => $name]
            ));
        }

        if (!is_string($helper) && !is_callable($this->helpers)) {
            throw new InvalidArgumentException($this->translate(
                'Invalid helper "{name}" type. Only classes or callable supported.',
                ['name' => $name]
            ));
        }

        $this->helpers[$name] = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function hasHelper($name)
    {
        return isset($this->helpers[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getList()
    {
        return array_keys($this->helpers);
    }

    /**
     * {@inheritdoc}
     */
    public function getCallable($name)
    {
        if (!isset($this->helpers[$name])) {
            throw new OutOfBoundsException($this->translate(
                'Templating helper "{name}" is not registered.',
                ['name' => $name]
            ));
        }

        $helper = $this->helpers[$name];
        if (is_string($helper)) {
            $this->helpers[$name] = $helper = $this->createTemplatingHelper($helper);
        }

        return $helper;
    }
}