<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\form;

/**
 * Интерфейс для компонентов, умеющих работать с формами.
 */
interface IFormAware
{
    /**
     * Устанавливает фабрику для создания элементов формы.
     * @param IEntityFactory $formFactory
     */
    public function setFormEntityFactory(IEntityFactory $formFactory);
}
