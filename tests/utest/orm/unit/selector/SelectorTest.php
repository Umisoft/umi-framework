<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\selector;

use umi\orm\collection\ICollectionFactory;
use umi\orm\objectset\ObjectSet;
use umi\orm\selector\Selector;
use umi\orm\toolbox\factory\ObjectSetFactory;
use umi\orm\toolbox\factory\SelectorFactory;
use utest\orm\ORMDbTestCase;

/**
 * Тест селектора
 *
 */
class SelectorTest extends ORMDbTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getCollectionConfig()
    {
        return [
            self::METADATA_DIR . '/mock/collections',
            [
                self::USERS_USER             => [
                    'type' => ICollectionFactory::TYPE_SIMPLE
                ]
            ],
            false
        ];
    }

    /**
     * @var Selector $selector
     */
    protected $selector;

    protected function setUpFixtures()
    {

        $objectsSet = new ObjectSet();
        $objectsSetFactory = new ObjectSetFactory();
        $selectorFactory = new SelectorFactory($objectsSetFactory);
        $this->resolveOptionalDependencies($selectorFactory);

        $this->selector = new Selector(
            $this->getCollectionManager()->getCollection(self::USERS_USER),
            $objectsSet,
            $selectorFactory
        );
        $this->resolveOptionalDependencies($this->selector);
    }

    public function testSelectorWithoutTypes()
    {
        $this->assertEquals(
            [],
            $this->selector->getSelectBuilder()
                ->getPlaceholderValues(),
            'Ожидается, что в запросе селектора не будут указаны конкретные типы, если не заданы ограничивающие выборку поля'
        );
    }

    public function testSelectorDetectAllTypes()
    {
        $this->selector->fields(['height', 'rating']);
        $this->assertEquals(
            [],
            $this->selector->getSelectBuilder()
                ->getPlaceholderValues(),
            'Ожидается, что в запросе селектора не будут указаны конкретные типы, если выбираемые поля присутствуют во всех типах коллекции'
        );
    }

    public function testSelectorDetectTypes()
    {
        $this->selector->where('supervisorField')
            ->isNull();
        $this->assertEquals(
            [':type_users_user' => ["users_user.supervisor"], ':value0' => [null, \PDO::PARAM_NULL]],
            $this->selector->getSelectBuilder()
                ->getPlaceholderValues(),
            'Ожидается, что в запросе селектора будут указаны только те типы, у которых есть участвующие в выборке поля.'
        );
    }

    public function testSelectorImpossibleTypesAndFieldCondition()
    {
        $this->selector->types(['base', 'supervisor'])
            ->where('supervisorField')
            ->isNull();
        $e = null;
        try {
            $this->selector->getSelectBuilder()
                ->getSql();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\InvalidArgumentException',
            $e,
            'Ожидается, что выборка не возможна, если участвующие поля не соответствуют заданным типам'
        );
    }

    public function testSelectorImpossibleFieldCondition()
    {
        $this->selector
            ->begin('OR')
            ->where('supervisorField')
            ->notEquals(170)
            ->where('guestField')
            ->isNull()
            ->end();
        $e = null;
        try {
            $this->selector->getSelectBuilder()
                ->getSql();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\InvalidArgumentException',
            $e,
            'Ожидается, что выборка не возможна, если комбинация условий не соответствует ни одному типу'
        );
    }

    public function testSelectorTypes()
    {
        $e = null;
        try {
            $this->selector->types(['wrong_type']);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при попытке указать несуществующий в коллекции тип'
        );

        $this->assertInstanceOf(
            'umi\orm\selector\ISelector',
            $this->selector->types(['supervisor', 'base']),
            'Ожидается, что ISelector::setTypes() вернет себя'
        );

        $this->assertEquals(
            [':type_users_user' => ["users_user.supervisor", "users_user.base"]],
            $this->selector->getSelectBuilder()
                ->getPlaceholderValues(),
            'Ожидается, что в запросе селектора будут участвовать заданные типы.'
        );
    }

    public function testChildrenTypes()
    {
        $this->assertInstanceOf(
            'umi\orm\selector\ISelector',
            $this->selector->types(['base*']),
            'Ожидается, что ISelector::setTypesWithChildren() вернет себя'
        );
        $this->assertEquals(
            [':type_users_user' => ["users_user.base", "users_user.guest", "users_user.supervisor"]],
            $this->selector->getSelectBuilder()
                ->getPlaceholderValues(),
            'Ожидается, что в запросе селектора будут участвовать заданные типы всей вложенности, если тип был указан со звездочкой.'
        );
    }

    public function testAllChildrenTypes()
    {
        $this->selector->types(['*']);
        $this->assertEquals(
            [':type_users_user' => ["users_user.base", "users_user.guest", "users_user.supervisor"]],
            $this->selector->getSelectBuilder()
                ->getPlaceholderValues(),
            'Ожидается, что в запросе селектора будут участвовать заданные типы всей вложенности, если тип был указан со звездочкой.'
        );
    }

    public function testSelectorFields()
    {

        $e = null;
        try {
            $this->selector->fields(['wrong_field']);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при попытке указать несуществующее у коллекции поле'
        );

        $this->assertInstanceOf(
            'umi\orm\selector\ISelector',
            $this->selector->fields(['height', 'rating']),
            'Ожидается, что ISelector::fields() вернет себя'
        );
        $this->assertEquals(
            'SELECT `users_user`.`id` AS `users_user:id`, `users_user`.`guid` AS `users_user:guid`, `users_user`.`type` AS `users_user:type`, `users_user`.`version` AS `users_user:version`, `users_user`.`height` AS `users_user:height`, `users_user`.`rating` AS `users_user:rating`
FROM `umi_mock_users` AS `users_user`
WHERE 1',
            $this->selector->getSelectBuilder()
                ->getSql(),
            'Ожидается, что в запросе селектора будут участвовать заданные 2 поля и 4 обязательных.'
        );
    }

    public function testSelectorLimit()
    {
        $this->assertInstanceOf(
            'umi\orm\selector\ISelector',
            $this->selector->limit(1, 3),
            'Ожидается, что ISelector::limit() вернет себя'
        );
        $this->assertEquals(
            [':limit_users_user' => [1, \PDO::PARAM_INT], ':offset_users_user' => [3, \PDO::PARAM_INT]],
            $this->selector->getSelectBuilder()
                ->getPlaceholderValues(),
            'Ожидается, что в запросе селектора будут участвовать заданные limit и offset.'
        );
    }

    public function testSelectorOrderBy()
    {

        $e = null;
        try {
            $this->selector->orderBy('wrong_field');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение при попытке задать сортировку по несуществующему у коллекции полю'
        );

        $this->assertInstanceOf(
            'umi\orm\selector\ISelector',
            $this->selector->orderBy('height'),
            'Ожидается, что ISelector::orderBy() вернет себя'
        );
        $this->selector->orderBy('rating', 'DESC');
        $this->selector->fields(['id']);

        $this->assertEquals(
            'SELECT `users_user`.`id` AS `users_user:id`, `users_user`.`guid` AS `users_user:guid`, `users_user`.`type` AS `users_user:type`, `users_user`.`version` AS `users_user:version`
FROM `umi_mock_users` AS `users_user`
WHERE 1
ORDER BY `users_user`.`height` ASC, `users_user`.`rating` DESC',
            $this->selector->getSelectBuilder()
                ->getSql(),
            'Неверный текст запроса с сортировкой'
        );
    }

    public function testFieldsConditions()
    {
        $condition = $this->selector->where('height');
        $condition->equals('163');
        $this->assertInstanceOf(
            'umi\orm\selector\condition\IFieldCondition',
            $condition,
            'Ожидается, что ISelector::where() вернет IFieldCondition'
        );

        $this->assertInstanceOf(
            'umi\orm\metadata\field\IField',
            $condition->getField(),
            'Ожидается, что для условия выставилось поле'
        );
        $this->assertEquals(
            'height',
            $condition->getField()
                ->getName(),
            'Ожидается, что для условия выставилось поле с именем height'
        );
        $this->assertEquals(
            '163',
            $condition->getExpression(),
            'Ожидается, что для условия выставилось значение для сравнения 163'
        );
        $this->assertEquals('=', $condition->getOperator(), 'Ожидается, что для условия выставился оператор "="');
        $this->assertEquals(
            ':value0',
            $condition->getPlaceholder(),
            'Ожидается, что для условия выставился плейсхолдер :value0'
        );

        $e = null;
        try {
            $this->selector->where('wrong_field');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение при попытке создать условие с несуществующим у коллекции полем'
        );

        $e = null;
        try {
            $this->selector->where('height', 'ru');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при попытке создать условие по несуществующей у поля локали'
        );
    }

    public function testConditionsGroup()
    {

        $this->selector
            ->fields(['id'])
            ->begin()
                ->begin('OR')
                    ->where('height')->less(170)
                    ->where('rating')->equals(5)
                ->end()
                ->begin('OR')
                    ->where('height')->more(154)
                    ->where('rating')->equalsOrMore(4)
                ->end()
            ->end();

        $this->assertEquals(
            'SELECT `users_user`.`id` AS `users_user:id`, `users_user`.`guid` AS `users_user:guid`, `users_user`.`type` AS `users_user:type`, `users_user`.`version` AS `users_user:version`
FROM `umi_mock_users` AS `users_user`
WHERE (((`users_user`.`height` < :value0 OR `users_user`.`rating` = :value1) AND (`users_user`.`height` > :value2 OR `users_user`.`rating` >= :value3)))',
            $this->selector->getSelectBuilder()
                ->getSql(),
            'Неверный текст запроса с группировками полей'
        );
    }

    public function testSelectorResult()
    {
        $this->assertInstanceOf(
            'umi\orm\objectset\IObjectSet',
            $this->selector->result(),
            'Ожидается, что ISelector::result() вернет IObjectSet'
        );
        $this->assertInstanceOf(
            'umi\orm\objectset\IObjectSet',
            $this->selector->getResult(),
            'Ожидается, что ISelector::getResult() вернет IObjectSet'
        );
    }
}
