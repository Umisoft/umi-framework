<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\form\unit\element;

use umi\form\element\Button;

/**
 * Тесты элемента формы - Кнопка
 */
class ButtonElementTest extends BaseElementTest
{

    /**
     * {@inheritdoc}
     */
    public function getElement($name, array $attributes = [], array $options = [])
    {
        $button = new Button($name, $attributes, $options);
        $button->setLabel('My element');

        $this->resolveOptionalDependencies($button);

        return $button;
    }

    public function testBasic()
    {
        $element = $this->getElement('testElement', ['data-id' => 'id'], ['value' => 'test value']);
        $element->setLabel('My element');

        $this->assertArrayHasKey(
            'data-id',
            $element->getAttributes()
                ->getArrayCopy(),
            'Ожидается, что аттрибуты будут установлены.'
        );

        $this->assertEquals('testElement', $element->getName(), 'Ожидается, что имя элемента будет установлено.');
        $this->assertEquals(
            'My element',
            $element->getValue(),
            'Ожидается, что значение по умолчанию будет всегда лейблом.'
        );
    }

    public function testValues()
    {
    }

    public function testFilters()
    {
    }
}