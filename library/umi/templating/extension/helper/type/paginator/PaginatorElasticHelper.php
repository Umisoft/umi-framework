<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\extension\helper\type\paginator;

use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\pagination\IPaginator;

/**
 * Помощник вида для пагинатора со стилем вывода "ELASTIC".
 */
class PaginatorElasticHelper extends PaginatorSlidingHelper implements ILocalizable
{

    use TLocalizable;

    /**
     * Возвращает массив страниц для отображения в ряду.
     * @param IPaginator $paginator объект пагинатора
     * @param int $pagesCountInRange количество страниц отображаемых в ряду
     * @return array массив ряда страниц для отображения
     */
    public function buildPagesRange(IPaginator $paginator, $pagesCountInRange)
    {
        $pagesCountInRange = $this->recalculatePagesCountInRange($paginator, $pagesCountInRange);

        return parent::buildPagesRange($paginator, $pagesCountInRange);
    }

    /**
     * Возвращает пересчитанное количество страниц, которые будут в отображаемом ряду.
     * @param IPaginator $paginator объект пагинатора
     * @param int $pagesCountInRange количество страниц отображаемых в ряду
     * @return int количество страниц в ряду
     */
    protected function recalculatePagesCountInRange(IPaginator $paginator, $pagesCountInRange)
    {
        $currentPage = $paginator->getCurrentPage();
        $pagesCount = $paginator->getPagesCount();
        $minPagesCountInRange = ceil($pagesCountInRange / 2);
        if ($currentPage <= $minPagesCountInRange) {
            $pagesCountInRange = $currentPage + $minPagesCountInRange - 1;
        } elseif ($pagesCount - $currentPage - 1 <= $minPagesCountInRange) {
            $pagesCountInRange = $pagesCountInRange - $minPagesCountInRange + 1;
        }

        return $pagesCountInRange;
    }
}
