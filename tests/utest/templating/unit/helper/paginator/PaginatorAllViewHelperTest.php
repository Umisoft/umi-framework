<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */


namespace utest\templating\unit\helper\paginator;

use umi\pagination\adapter\ArrayPaginationAdapter;
use umi\pagination\Paginator;
use umi\templating\extension\helper\type\paginator\PaginatorAllHelper;
use utest\TestCase;

/**
 * Тестирования пагинатора типа "All"
 */
class PaginatorAllHelperTest extends TestCase
{

    /**
     * Тест помощника вида пагинатора со стилевым отображением типа "All"
     */
    public function testBasic()
    {
        $adapter = new ArrayPaginationAdapter(range(0, 99));
        $paginator = new Paginator($adapter, 10);
        $paginator->setCurrentPage(5);
        $Helper = new PaginatorAllHelper();
        $context = [
            'firstPage'         => 1,
            'lastPage'          => 10,
            'currentPage'       => 5,
            'pagesCount'        => 10,
            'itemsPerPage'      => 10,
            'previousPage'      => 4,
            'nextPage'          => 6,
            'pagesRange'        => range(1, 10),
            'currentItemsCount' => 10,
            'itemsCount'        => 100,
            'firstItemNumber'   => 41,
            'lastItemNumber'    => 50,
        ];
        $this->assertEquals($context, $Helper($paginator), 'Ожидается, что контекст будет сформирован верно.');
    }
}

?>