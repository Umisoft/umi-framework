<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\pagination;

use umi\i18n\ILocalizable;
use umi\pagination\exception\OutOfBoundsException;
use umi\pagination\exception\UnexpectedValueException;

/**
 * Интерфейс пагинатора.
 */
interface IPaginator extends ILocalizable
{

    /**
     * Возвращет количество элементов, выводимых на страницу.
     * @return int
     */
    public function getItemsPerPage();

    /**
     * Возвращает номер текущей страницы.
     * @return int
     */
    public function getCurrentPage();

    /**
     * Устанавливает номер текущей страницы.
     * @param int $page
     * @throws OutOfBoundsException если такой номер страницы не возможен при заданных настройках
     */
    public function setCurrentPage($page);

    /**
     * Возвращает количество страниц.
     * @return int $page
     */
    public function getPagesCount();

    /**
     * Возвращает общее количество элементов.
     * @return int
     */
    public function getItemsCount();

    /**
     * Возвращает набор элементов на странице.
     * @throws UnexpectedValueException при несоответсвии набора элементов заданным типам
     * @return array|\Traversable
     */
    public function getPageItems();
}
