<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\event\toolbox;

use umi\event\IEventFactory;
use umi\event\IEventObservant;
use umi\toolkit\exception\UnsupportedServiceException;
use umi\toolkit\toolbox\IToolbox;
use umi\toolkit\toolbox\TToolbox;

/**
 * Инструменты для поддержки событий.
 */
class EventTools implements IToolbox
{
    /**
     * Имя набора инструментов.
     */
    const NAME = 'event';

    use TToolbox;

    /**
     * @var string $eventFactoryClass класс для создания фабрики событий и менеджеров событий
     */
    public $eventFactoryClass = 'umi\event\toolbox\factory\EventFactory';

    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->registerFactory(
            'eventFactory',
            $this->eventFactoryClass,
            ['umi\event\IEventFactory']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getService($serviceInterfaceName, $concreteClassName)
    {
        switch ($serviceInterfaceName) {
            case 'umi\event\IEventManager':
                return $this->getEventFactory()->createEventManager();
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
        if ($object instanceof IEventObservant) {
            $object->setEventFactory($this->getEventFactory());
        }
    }

    /**
     * Создает и возвращает фабрику менеджеров событий.
     * @return IEventFactory
     */
    protected function getEventFactory()
    {
        return $this->getFactory('eventFactory');
    }

}
