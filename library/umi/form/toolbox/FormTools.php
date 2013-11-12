<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\form\toolbox;

use umi\form\IEntityFactory;
use umi\form\IFormAware;
use umi\toolkit\toolbox\IToolbox;
use umi\toolkit\toolbox\TToolbox;

/**
 * Набор инструментов для работы с формами.
 */
class FormTools implements IToolbox
{
    /**
     * Имя набора инструментов
     */
    const NAME = 'form';

    use TToolbox;

    /**
     * @var string $elementFactoryClass класс фабрики
     */
    public $entityFactoryClass = 'umi\form\toolbox\factory\EntityFactory';

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->registerFactory('entity', $this->entityFactoryClass, ['umi\form\IEntityFactory']);
    }

    /**
     * {@inheritdoc}
     */
    public function injectDependencies($object)
    {
        if ($object instanceof IFormAware) {
            $object->setFormEntityFactory($this->getEntityFactory());
        }
    }

    /**
     * Возвращает фабрику элементов формы.
     * @return IEntityFactory
     */
    protected function getEntityFactory()
    {
        return $this->getFactory('entity');
    }
}