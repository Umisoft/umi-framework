<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\toolbox\factory;

use umi\hmvc\exception\OutOfBoundsException;
use umi\hmvc\model\IModelAware;
use umi\hmvc\model\IModelFactory;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;
use umi\toolkit\prototype\IPrototype;

/**
 * Class ModelFactory
 */
class ModelFactory implements IModelFactory, IFactory
{
    use TFactory;

    /**
     * @var array $modelsList список моделей
     */
    protected $modelsList = [];

    /**
     * Конструктор.
     * @param array $options опции
     */
    public function __construct(array $options)
    {
        $this->modelsList = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function createByName($name, $args = [])
    {
        if (!isset($this->modelsList[$name])) {
            throw new OutOfBoundsException($this->translate(
                'Model "{name}" has not registered.',
                ['name' => $name]
            ));
        }

        return $this->createModel($this->modelsList[$name], $args);
    }

    /**
     * {@inheritdoc}
     */
    public function createByClass($class, $args = [])
    {
        if (array_search($class, $this->modelsList) === false) {
            throw new OutOfBoundsException($this->translate(
                'Cannot create model by class "{class}". Model class not registered.',
                ['class' => $class]
            ));
        }

        return $this->createModel($class, $args);
    }

    /**
     * Создает модель с заданным классом.
     * @param string $class
     * @param array $args
     * @return object
     */
    protected function createModel($class, array $args)
    {
        return $this->getPrototype(
            $class,
            [],
            function (IPrototype $prototype)
            {
                $prototype->registerConstructorDependency(
                    'umi\hmvc\model\IModel',
                    function ($concreteClassName) {
                        return $this->createByClass($concreteClassName);
                    }
                );

                $prototypeInstance = $prototype->getPrototypeInstance();
                if ($prototypeInstance instanceof IModelAware) {
                    $prototypeInstance->setModelFactory($this);
                }
            }
        )->createInstance($args);
    }
}