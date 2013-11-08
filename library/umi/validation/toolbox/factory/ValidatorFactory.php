<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\validation\toolbox\factory;

use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;
use umi\validation\exception\OutOfBoundsException;
use umi\validation\IValidatorFactory;

/**
 * Фабрика для создания валидаторов.
 */
class ValidatorFactory implements IValidatorFactory, IFactory
{

    use TFactory;

    /**
     * @var array $types поддерживаемые валидаторы
     */
    public $types = array(
        self::TYPE_EMAIL    => 'umi\validation\type\Email',
        self::TYPE_REGEXP   => 'umi\validation\type\Regexp',
        self::TYPE_REQUIRED => 'umi\validation\type\Required',
    );
    /**
     * @var string $validatorCollectionClass класс коллекции валидаторов
     */
    public $validatorCollectionClass = 'umi\validation\ValidatorCollection';

    /**
     * {@inheritdoc}
     */
    public function createValidatorCollection(array $config)
    {
        $validators = [];

        foreach ($config as $type => $options) {
            $validators[$type] = $this->createValidator($type, $options);
        }

        return $this->createInstance(
            $this->validatorCollectionClass,
            [$validators],
            ['umi\validation\IValidatorCollection']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createValidator($type, array $options = [])
    {
        if (!isset($this->types[$type])) {
            throw new OutOfBoundsException($this->translate(
                'Validator "{type}" is not available.',
                ['type' => $type]
            ));
        }

        return $this->createInstance(
            $this->types[$type],
            [$options],
            ['umi\validation\IValidator']
        );
    }
}