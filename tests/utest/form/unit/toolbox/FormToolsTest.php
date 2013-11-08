<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\form\unit;

use umi\form\toolbox\FormTools;
use utest\TestCase;

/**
 * Тесты инструментов для работы с формами.
 */
class FormToolsTest extends TestCase
{
    /**
     * @var FormTools $tools инструментарий для работы с формами
     */
    protected $tools;

    public function setUpFixtures()
    {
        $this->tools = new FormTools();
        $this->resolveOptionalDependencies($this->tools);
    }

    /**
     * Тест создания формы.
     */
    public function testFormCreation()
    {
        $form = $this->tools->getEntityFactory()
            ->createForm(
            [
                'action' => '/',
                'elements' => [
                    'test' => []
                ]
            ]
        );

        $this->assertInstanceOf('umi\form\Form', $form, 'Ожидается, что форма будет создана.');
        $this->assertInstanceOf(
            'umi\form\element\Text',
            $form->getElement('test'),
            'Ожидается, что форма будет содержать элемент.'
        );
    }
}