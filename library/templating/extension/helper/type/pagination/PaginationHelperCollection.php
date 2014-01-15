<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace umi\templating\extension\helper\type\pagination;

use umi\pagination\IPaginator;
use umi\templating\exception\InvalidArgumentException;

/**
 * Коллекция помощников вида для пагинатора.
 */
class PaginationHelperCollection
{
    /**
     * Помощник вида для пагинатора со стилем вывода "ALL".
     */
    public function all(IPaginator $paginator)
    {
        return $this->buildContext($paginator) + [
            'pagesRange' => range(1, $paginator->getPagesCount())
        ];
    }

    /**
     * Помощник вида для пагинатора со стилем вывода "ELASTIC".
     */
    public function elastic(IPaginator $paginator, $pagesCountInRange)
    {
        $this->checkPagesCountInRange($pagesCountInRange);

        return $this->buildContext($paginator) + [
            'pagesRange' => $this->elasticBuildPagesRange($paginator, $pagesCountInRange)
        ];
    }

    /**
     * Помощник вида для пагинатора со стилем вывода "JUMPING".
     */
    public function jumping(IPaginator $paginator, $pagesCountInRange)
    {
        $this->checkPagesCountInRange($pagesCountInRange);

        return $this->buildContext($paginator) + [
            'pagesRange' => $this->jumpingBuildPagesRange($paginator, $pagesCountInRange)
        ];
    }

    /**
     * Помощник вида для пагинатора со стилем вывода "SLIDING".
     */
    public function sliding(IPaginator $paginator, $pagesCountInRange)
    {
        $this->checkPagesCountInRange($pagesCountInRange);

        return $this->buildContext($paginator) + [
            'pagesRange' => $this->slidingBuildPagesRange($paginator, $pagesCountInRange)
        ];
    }

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

    /**
     * Возвращает массив страниц для отображения в ряду.
     * @param IPaginator $paginator объект пагинатора
     * @param int $pagesCountInRange количество страниц отображаемых в ряду
     * @return array
     */
    protected function slidingBuildPagesRange(IPaginator $paginator, $pagesCountInRange)
    {
        $currentPage = $paginator->getCurrentPage();
        $pagesCount = $paginator->getPagesCount();
        $rangeStart = $currentPage - ceil($pagesCountInRange / 2);
        $lastPossibleStart = $pagesCount - $pagesCountInRange + 1;
        if ($rangeStart <= 0) {
            $rangeEnd = $pagesCountInRange >= $pagesCount ? $pagesCount : $pagesCountInRange;

            return range(1, $rangeEnd);
        } elseif ($rangeStart >= $lastPossibleStart) {
            $lastPossibleStart = $lastPossibleStart ?: 1;

            return range($lastPossibleStart, $pagesCount);
        } else {
            return range($rangeStart + 1, $rangeStart + $pagesCountInRange);
        }
    }

    /**
     * Возвращает массив страниц для отображения в ряду.
     * @param IPaginator $paginator объект пагинатора
     * @param int $pagesCountInRange количество страниц отображаемых в ряду
     * @return array массив ряда страниц для отображения
     */
    protected function elasticBuildPagesRange(IPaginator $paginator, $pagesCountInRange)
    {
        $currentPage = $paginator->getCurrentPage();
        $pagesCount = $paginator->getPagesCount();
        $minPagesCountInRange = ceil($pagesCountInRange / 2);
        if ($currentPage <= $minPagesCountInRange) {
            $pagesCountInRange = $currentPage + $minPagesCountInRange - 1;
        } elseif ($pagesCount - $currentPage - 1 <= $minPagesCountInRange) {
            $pagesCountInRange = $pagesCountInRange - $minPagesCountInRange + 1;
        }

        return $this->slidingBuildPagesRange($paginator, $pagesCountInRange);
    }

    /**
     * Возвращает массив номеров страниц для отображения в ряду.
     * @param IPaginator $paginator объект пагинатора
     * @param int $pagesCountInRange количество страниц отображаемых в ряду
     * @return array
     */
    protected function jumpingBuildPagesRange(IPaginator $paginator, $pagesCountInRange)
    {
        $currentPage = $paginator->getCurrentPage();
        $pagesCount = $paginator->getPagesCount();
        $currentRange = ceil($currentPage / $pagesCountInRange);
        $ragesCount = ceil($pagesCount / $pagesCountInRange);
        if ($currentRange == 1) {
            $rangeEnd = $pagesCount < $pagesCountInRange ? $pagesCount : $pagesCountInRange;

            return range(1, $rangeEnd);
        }
        $rangeStart = ($currentRange - 1) * $pagesCountInRange + 1;
        if ($currentRange < $ragesCount) {
            return range($rangeStart, $rangeStart + $pagesCountInRange - 1);
        } else {
            return range($rangeStart, $pagesCount);
        }
    }

    private function checkPagesCountInRange($pagesCountInRange)
    {
        if ($pagesCountInRange <= 0 || !is_int($pagesCountInRange)) {
            throw new InvalidArgumentException(
                sprintf('%s is wrong pages count in range. Value should be positive integer.', $pagesCountInRange)
            );
        }
    }
}
