<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\extension\helper\type\paginator;

use umi\pagination\IPaginator;

/**
 * Абстрактный класс помощника вида пагинатора.
 */
abstract class AbstractPaginatorHelper
{

    /**
     * Возвращает контекс пагинатора.
     * @param IPaginator $paginator
     * @return array
     */
    protected function buildContext(IPaginator $paginator)
    {
        $firstItemNumber = ($paginator->getCurrentPage() - 1) * $paginator->getItemsPerPage();
        $currentItemsCount = $paginator->getCurrentPage() < $paginator->getPagesCount() ? $paginator->getItemsPerPage(
        ) : $paginator->getItemsCount() - $firstItemNumber;

        return [
            'firstPage'         => 1,
            'lastPage'          => $paginator->getPagesCount(),
            'currentPage'       => $paginator->getCurrentPage(),
            'pagesCount'        => $paginator->getPagesCount(),
            'itemsPerPage'      => $paginator->getItemsPerPage(),
            'previousPage'      => ($paginator->getCurrentPage() > 1) ? $paginator->getCurrentPage() - 1 : null,
            'nextPage'          => ($paginator->getCurrentPage() < $paginator->getPagesCount(
                    )) ? $paginator->getCurrentPage() + 1 : null,
            'currentItemsCount' => $currentItemsCount,
            'itemsCount'        => $paginator->getItemsCount(),
            'firstItemNumber'   => $firstItemNumber + 1,
            'lastItemNumber'    => $firstItemNumber + $currentItemsCount
        ];
    }
}
