<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\extension\adapter;

use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\templating\exception\OutOfBoundsException;
use umi\templating\extension\helper\collection\IHelperCollection;

/**
 * Class ExtensionAdapter
 */
class ExtensionAdapter implements IExtensionAdapter, ILocalizable
{
    use TLocalizable;

    /**
     * @var array $collections коллекции помощников для шаблонов
     */
    protected $collections = [];
    /**
     * @var bool $sorted состояние списка коллекций
     */
    private $sorted = true;

    /**
     * {@inheritdoc}
     */
    public function addHelperCollection($name, IHelperCollection $collection, $priority = 0)
    {
        $this->sorted = false;

        $this->collections[$name] = [$priority, $collection];
    }

    /**
     * {@inheritdoc}
     */
    public function getHelperCollection($name)
    {
        if (!isset($this->collections[$name])) {
            throw new OutOfBoundsException($this->translate(
                'Cannot return helper collection. Invalid helper collection name "{name}".',
                ['name' => $name]
            ));
        }

        return $this->collections[$name][1];
    }

    /**
     * {@inheritdoc}
     */
    public function getRegisteredHelperCollection()
    {
        $this->resortCollections();

        return array_keys($this->collections);
    }

    /**
     * {@inheritdoc}
     */
    public function removeHelperCollection($name)
    {
        unset($this->collections[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getList()
    {
        $names = [];
        foreach ($this->collections as $obj) {
            /** @var IHelperCollection $collection */
            $collection = $obj[1];

            $names += $collection->getList();
        }

        return $names;
    }

    /**
     * {@inheritdoc}
     */
    public function getCallable($name)
    {
        $this->resortCollections();

        foreach ($this->collections as $obj) {
            /** @var IHelperCollection $collection */
            $collection = $obj[1];

            if ($collection->hasHelper($name)) {
                return $collection->getCallable($name);
            };
        }

        throw new OutOfBoundsException($this->translate(
            'Templating helper "{name}" is not registered.',
            ['name' => $name]
        ));
    }

    protected function resortCollections()
    {
        if (!$this->sorted) {
            uasort(
                $this->collections,
                function ($a, $b) {
                    return $a[0] < $b[0];
                }
            );
        }
    }
}
 