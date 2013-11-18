<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\session\toolbox\factory;

use umi\session\entity\factory\ISessionEntityFactory;
use umi\session\entity\validator\ISessionValidator;
use umi\session\exception\OutOfBoundsException;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика для создания пространств имен в сессии.
 */
class SessionEntityFactory implements ISessionEntityFactory, IFactory
{

    use TFactory;

    /**
     * @var string $namespaceClass класс пространства имен сессии
     */
    public $namespaceClass = 'umi\session\entity\ns\SessionNamespace';
    /**
     * @var array $validatorClasses классы валидаторов
     */
    public $validatorClasses = [
        ISessionValidator::LIFE_TIME  => 'umi\session\entity\validator\Lifetime',
        ISessionValidator::USER_AGENT => 'umi\session\entity\validator\UserAgent',
    ];

    /**
     * @var array $storageClasses классы хранилищ сессии
     */
    public $storageClasses = [
        'system' => '\SessionHandler'
    ];

    /**
     * {@inheritdoc}
     */
    public function createSessionNamespace($name)
    {
        return $this->getPrototype(
                $this->namespaceClass,
                ['umi\session\entity\ns\ISessionNamespace']
            )
            ->createInstance([$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function createSessionValidator($type, $options)
    {
        if (!isset($this->validatorClasses[$type])) {
            throw new OutOfBoundsException($this->translate(
                'Validator "{type}" is not exist.',
                ['type' => $type]
            ));
        }

        return $this->getPrototype(
                $this->validatorClasses[$type],
                ['umi\session\entity\validator\ISessionValidator']
            )
            ->createInstance([$options]);
    }

    /**
     * {@inheritdoc}
     */
    public function createSessionStorage($type, array $options = [])
    {
        if (!isset($this->storageClasses[$type])) {
            throw new OutOfBoundsException($this->translate(
                'Session storage "{storage}" not found.',
                ['storage' => $type]
            ));
        }

        return $this->getPrototype(
                $this->storageClasses[$type],
                ['\SessionHandlerInterface']
            )
            ->createInstance([$options]);
    }
}