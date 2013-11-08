<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\metadata\field\relation;

use umi\orm\metadata\field\relation\ManyToManyRelationField;
use utest\orm\unit\metadata\field\FieldTestCase;

/**
 * Тест поля связи с типом "многие-ко-многим".
 */
class ManyToManyRelationFieldTest extends FieldTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getField()
    {
        return new ManyToManyRelationField('mock', [
            'target'       => 'targetCollection',
            'targetField'  => 'targetField',
            'relatedField' => 'relatedField',
            'bridge'       => 'bridgeCollection'
        ]);
    }

    public function testConfig()
    {

        $e = null;
        try {
            new ManyToManyRelationField('field');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\UnexpectedValueException',
            $e,
            'Ожидается исключение при попытке создать поле ManyToManyRelation без указания целевой коллекции'
        );

        $config = ['target' => 'targetCollection'];
        $e = null;
        try {
            new ManyToManyRelationField('field', $config);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\UnexpectedValueException',
            $e,
            'Ожидается исключение при попытке создать поле ManyToManyRelation без указания поля для связи в целевой коллекции'
        );

        $config['targetField'] = 'targetField';
        $e = null;
        try {
            new ManyToManyRelationField('field', $config);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\UnexpectedValueException',
            $e,
            'Ожидается исключение при попытке создать поле ManyToManyRelation без указания поля для связи в коллекции-мосте'
        );

        $config['relatedField'] = 'relatedField';
        $e = null;
        try {
            new ManyToManyRelationField('field', $config);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\UnexpectedValueException',
            $e,
            'Ожидается исключение при попытке создать поле ManyToManyRelation без указания коллекции-моста'
        );

        $config['bridge'] = 'bridgeCollection';
        $field = new ManyToManyRelationField('field', $config);

        $this->assertEquals('targetCollection', $field->getTargetCollectionName(), 'Неверно прочитан конфиг');
        $this->assertEquals('targetField', $field->getTargetFieldName(), 'Неверно прочитан конфиг');
        $this->assertEquals('bridgeCollection', $field->getBridgeCollectionName(), 'Неверно прочитан конфиг');
        $this->assertEquals('relatedField', $field->getRelatedFieldName(), 'Неверно прочитан конфиг');
    }

    public function testMethods()
    {

        $field = new ManyToManyRelationField('mock', [
            'target'       => 'targetCollection',
            'targetField'  => 'targetField',
            'relatedField' => 'relatedField',
            'bridge'       => 'bridgeCollection'
        ]);

        $this->assertNull($field->getDataType(), 'Ожидается, что тип данных у поля многие ко многим - null');

    }

}
