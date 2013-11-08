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
use umi\templating\exception\InvalidArgumentException;

/**
 * Помощник вида для пагинатора со стилем вывода "SLIDING".
 */
class PaginatorSlidingHelper extends AbstractPaginatorHelper implements ILocalizable
{

    use TLocalizable;

    /**
     * Возвращает контекст пагинатора.
     * @param IPaginator $paginator объект пагинатора
     * @param int $pagesCountInRange количество страниц отображаемых в ряду
     * @throws InvalidArgumentException если передано отрицательно кол-во страниц в ряду
     * @return array контекст
     */
    public function __invoke(IPaginator $paginator, $pagesCountInRange)
    {
        if ($pagesCountInRange <= 0 || !is_int($pagesCountInRange)) {
            throw new InvalidArgumentException($this->translate(
                'Pages count in range should be positive integer.'
            ));
        }

        return $this->buildContext($paginator) + [
            'pagesRange' => $this->buildPagesRange($paginator, $pagesCountInRange)
        ];
    }

    /**
     * Возвращает массив страниц для отображения в ряду.
     * @param IPaginator $paginator объект пагинатора
     * @param int $pagesCountInRange количество страниц отображаемых в ряду
     * @return array
     */
    protected function buildPagesRange(IPaginator $paginator, $pagesCountInRange)
    {
        $currentPage = $paginator->getCurrentPage();
        $pagesCount = $paginator->getPagesCount();
        $rangeStart = $currentPage - ceil($pagesCountInRange / 2);
        $lastPossibleStart = $pagesCount - $pagesCountInRange + 1;
        if ($rangeStart <= 0) {
            $rangeEnd = $pagesCountInRange >= $pagesCount ? $pagesCount : $pagesCountInRange;

            return range(1, $rangeEnd);
        } elseif ($rangeStart >= $lastPossibleStart) {
            return range($lastPossibleStart, $pagesCount);
        } else {
            return range($rangeStart + 1, $rangeStart + $pagesCountInRange);
        }
    }
}
