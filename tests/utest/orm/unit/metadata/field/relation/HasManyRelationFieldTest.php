<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\metadata\field\relation;

use umi\orm\metadata\field\relation\HasManyRelationField;
use utest\orm\unit\metadata\field\FieldTestCase;

class HasManyRelationFieldTest extends FieldTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getField()
    {
        return new HasManyRelationField('mock', [
            'target'      => 'targetCollection',
            'targetField' => 'targetField'
        ]);
    }

    public function testConfig()
    {
        $e = null;
        try {
            new HasManyRelationField('mock');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\UnexpectedValueException',
            $e,
            'Ожидается исключение при попытке создать поле HasManyRelation без указания целевой коллекции'
        );

        $config = ['target' => 'targetCollection'];
        $e = null;
        try {
            new HasManyRelationField('mock', $config);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\UnexpectedValueException',
            $e,
            'Ожидается исключение при попытке создать поле ManyToManyRelation без указания поля для связи в целевой коллекции'
        );

        $config['targetField'] = 'targetField';
        $field1 = new HasManyRelationField('mock', $config);

        $this->assertEquals('targetCollection', $field1->getTargetCollectionName(), 'Неверно прочитан конфиг');
        $this->assertEquals('targetField', $field1->getTargetFieldName(), 'Неверно прочитан конфиг');

    }

    public function testMethods()
    {

        $field = new HasManyRelationField('mock', [
            'target'      => 'targetCollection',
            'targetField' => 'targetField'
        ]);

        $this->assertNull($field->getDataType(), 'Ожидается, что тип данных у поля один ко многим - null');

    }

}
