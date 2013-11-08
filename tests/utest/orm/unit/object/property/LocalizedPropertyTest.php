<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\object\property;

use umi\orm\metadata\field\string\TextField;
use umi\orm\object\IObject;
use umi\orm\object\property\LocalizedProperty;
use utest\TestCase;

/**
 * Тест локализованного свойства
 */
class LocalizedPropertyTest extends TestCase
{

    public function testLocalizedProperty()
    {

        /**
         * @var IObject $object
         */
        $object = $this->getMock('umi\orm\object\Object', [], [], '', false);

        $localizedField = new TextField('title', [
            'localizations' => [
                'en' => ['columnName' => 'title_en'],
                'ru' => ['columnName' => 'title']
            ]
        ]);
        $localizedProperty = new LocalizedProperty($object, $localizedField, 'en');

        $this->assertEquals(
            'en',
            $localizedProperty->getLocaleId(),
            'Ожидается, что у локализованного свойства есть локаль'
        );
        $this->assertEquals(
            'title#en',
            $localizedProperty->getFullName(),
            'Ожидается, что у локализованного свойства полное имя содержит имя локали'
        );

    }

}
