<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace umi\event;

/**
 * Фабрика событий и менеджеров событий.
 */
interface IEventFactory {

    /**
     * Создает и возвращает новый менеджер событий
     * @return IEventManager
     */
    public function createEventManager();

    /**
     * Создает и возвращает новое событие
     * @param string $type тип события
     * @param mixed $target объект, в котором произошло событие
     * @param array $params список параметров события ['paramName' => 'paramVal', 'relParam' => &$var]
     * @param array $tags список тэгов, с которыми произошло событие
     * @return IEvent
     */
    public function createEvent($type, $target, array $params = [], array $tags = []);
}
 