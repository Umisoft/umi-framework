<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\metadata\field;

use umi\orm\collection\ICollection;

/**
 * Интерфейс для полей связи.
 */
interface IRelationField extends IField
{

    /**
     * Возвращает имя коллекции на которую выставлена связь
     * @return string
     */
    public function getTargetCollectionName();

    /**
     * Возвращает коллекцию на которую выставлена связь
     * @return ICollection
     */
    public function getTargetCollection();
}
