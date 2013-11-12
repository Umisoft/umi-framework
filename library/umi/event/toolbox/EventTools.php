<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\event\toolbox;

use umi\toolkit\toolbox\TToolbox;

/**
 * Инструменты для поддержки событий.
 */
class EventTools implements IEventTools
{
    /**
     * Имя набора инструментов
     */
    const NAME = 'event';

    use TToolbox;

    /**
     * @var string $eventManagerClass класс для создания менеджера событий
     */
    public $eventManagerClass = 'umi\event\EventManager';
    /**
     * @var array $manager опции менеджера события
     */
    public $manager = [];

    /**
     * {@inheritdoc}
     */
    public function getObjectInitializers($prototype, $factory)
    {
        $initializers = [];
        if ($prototype instanceof IEventObservant) {
            $initializers[] = function (IEventObservant $object, $factory) {
                $object->setEventManager($this->createEventManager());
                if ($factory instanceof IEventObservant) {
                    $factory->subscribeTo($object);
                }
                $object->bindLocalEvents();
            };
        }

        return $initializers;
    }

    /**
     * {@inheritdoc}
     */
    public function createEventManager()
    {
        return $this->createInstance($this->eventManagerClass, [], ['umi\event\IEventManager'], $this->manager);
    }

}
