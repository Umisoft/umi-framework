<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\validation\toolbox;

use umi\toolkit\exception\UnsupportedServiceException;
use umi\toolkit\toolbox\IToolbox;
use umi\toolkit\toolbox\TToolbox;
use umi\validation\IValidationAware;
use umi\validation\IValidatorFactory;

/**
 * Набор инструментов валидации.
 */
class ValidationTools implements IToolbox
{
    /**
     * Имя набора инструментов
     */
    const NAME = 'validation';

    use TToolbox;

    /**
     * @var string $validatorFactoryClass класс фабрики валидаторов
     */
    public $validatorFactoryClass = 'umi\validation\toolbox\factory\ValidatorFactory';

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->registerFactory(
            'validator',
            $this->validatorFactoryClass,
            ['umi\validation\IValidatorFactory']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getService($serviceInterfaceName, $concreteClassName)
    {
        switch ($serviceInterfaceName) {
            case 'umi\validation\IValidatorFactory':
                return $this->getValidatorFactory();
        }
        throw new UnsupportedServiceException($this->translate(
            'Toolbox "{name}" does not support service "{interface}".',
            ['name' => self::NAME, 'interface' => $serviceInterfaceName]
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function injectDependencies($object)
    {
        if ($object instanceof IValidationAware) {
            $object->setValidatorFactory($this->getValidatorFactory());
        }
    }

    /**
     * Возвращает фабрику для создания валидаторов.
     * @return IValidatorFactory
     */
    protected function getValidatorFactory()
    {
        return $this->getFactory('validator');
    }
}