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
 * Помощник вида для пагинатора со стилем вывода "ALL".
 */
class PaginatorAllHelper extends AbstractPaginatorHelper
{

    /**
     * Возвращает контекст пагинатора
     * @param IPaginator $paginator
     * @return array контекст
     */
    public function __invoke(IPaginator $paginator)
    {
        return $this->buildContext($paginator) + [
            'pagesRange' => range(1, $paginator->getPagesCount())
        ];
    }
}
