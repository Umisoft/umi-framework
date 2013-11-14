<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\func\selector;

use umi\orm\object\IObject;
use umi\orm\selector\ISelector;
use umi\orm\selector\Selector;
use utest\orm\mock\collections\users\Supervisor;
use utest\orm\mock\collections\users\User;
use utest\orm\ORMDbTestCase;

/**
 * Тест селектора
 *
 */
class SelectorTest extends ORMDbTestCase
{

    /**
     * @var Selector $selector
     */
    protected $selector;

    /**
     * {@inheritdoc}
     */
    protected function getCollections()
    {
        return array(
            self::USERS_USER,
            self::USERS_GROUP
        );
    }

    protected function setUpFixtures()
    {

        $userCollection = $this->collectionManager->getCollection(self::USERS_USER);
        /**
         * @var User $user1
         * @var User $user2
         * @var User $user3
         * @var Supervisor $sv1
         * @var Supervisor $sv2
         */
        $user1 = $userCollection->add();
        $user1->login = 'simply_user';
        $user1->setValue('rating', 7.5);

        $sv1 = $userCollection->add('supervisor');
        $sv1->login = 'supervisor';
        $sv1->setValue('height', 167);
        $sv1->setValue('rating', 5);

        $user2 = $userCollection->add();;
        $user2->login = 'test_user';
        $user2->setValue('height', 183);
        $user2->setValue('rating', 4.2);

        $user2 = $userCollection->add();;
        $user2->login = 'user_test';
        $user2->setValue('height', 181);
        $user2->setValue('rating', 7.2);

        $sv2 = $userCollection->add('supervisor');
        $sv2->login = 'admin';
        $sv2->setValue('height', 170);

        $this->objectPersister->commit();

        $this->selector = $userCollection->select();

    }

    protected function tearDownFixtures()
    {
        $this->getDbCluster()
            ->modifyInternal('DROP TABLE IF EXISTS `umi_mock_users`');
    }

    public function testSelector()
    {
        $result = $this->selector->getResult();
        $this->assertCount(
            5,
            $result->fetchAll(),
            'Ожидается, что выборка по всей коллекции вернет objectsSet из 5 объектов'
        );
    }

    public function testSelectorBaseType()
    {
        $this->selector->types(['base']);
        $result = $this->selector->getResult();
        $this->assertCount(
            3,
            $result->fetchAll(),
            'Ожидается, что выборка c указанием типа Base вернет objectsSet из 3 объектов'
        );
    }

    public function testSelectorOtherType()
    {
        $this->selector->types(['supervisor']);
        $result = $this->selector->getResult();
        $this->assertCount(
            2,
            $result->fetchAll(),
            'Ожидается, что выборка c указанием типа supervisor вернет objectsSet из 2 объектов'
        );
    }

    public function testSelectorLimit()
    {
        $this->selector->limit(3);
        $result = $this->selector->getResult();
        $this->assertCount(
            3,
            $result->fetchAll(),
            'Ожидается, что выборка c указанием лимита 3 вернет objectsSet из 3 объектов'
        );
    }

    public function testSelectorOffset()
    {
        $this->selector->limit(3, 3);
        $result = $this->selector->getResult();
        $this->assertCount(
            2,
            $result->fetchAll(),
            'Ожидается, что выборка c указанием limit 3 и offset 3 вернет objectsSet из 2 объектов'
        );
    }

    public function testSelectorOrderBy()
    {
        $this->selector
            ->orderBy('type')
            ->orderBy('rating', 'DESC');
        $result = $this->selector->getResult();

        $firstObject = $result->fetch();
        $secondObject = $result->fetch();

        $this->assertEquals(
            'simply_user',
            $firstObject->getValue('login'),
            'Ожидается, что первым в выборке будет объект с логином simply_user'
        );
        $this->assertEquals(
            'user_test',
            $secondObject->getValue('login'),
            'Ожидается, что вторым в выборке будет объект с логином user_test'
        );

    }

    public function testSelectorWhereEquals()
    {
        $this->selector->where('height')
            ->equals(170);
        $result = $this->selector->getResult();
        $this->assertCount(
            1,
            $result->fetchAll(),
            'Ожидается, что выборка c ограничением по полу height равному 170 вернет objectsSet из 1 объекта'
        );
    }

    public function testSelectorWhereNotEquals()
    {
        $this->selector->where('height')
            ->notEquals(170);
        $result = $this->selector->getResult();
        $this->assertCount(
            3,
            $result->fetchAll(),
            'Ожидается, что выборка c ограничением по полу height не равному 170 вернет objectsSet из 3 объекта'
        );
    }

    public function testSelectorWhereLess()
    {
        $this->selector->where('height')
            ->less(181);
        $result = $this->selector->getResult();
        $this->assertCount(
            2,
            $result->fetchAll(),
            'Ожидается, что выборка c ограничением по полу height меньше 181 вернет objectsSet из 2 объектов'
        );
    }

    public function testSelectorWhereMore()
    {
        $this->selector->where('height')
            ->more(181);
        $result = $this->selector->getResult();
        $this->assertCount(
            1,
            $result->fetchAll(),
            'Ожидается, что выборка c ограничением по полу height больше 181 вернет objectsSet из 1 объекта'
        );
    }

    public function testSelectorWhereLessOrEquals()
    {
        $this->selector->where('height')
            ->equalsOrLess(181);
        $result = $this->selector->getResult();
        $this->assertCount(
            3,
            $result->fetchAll(),
            'Ожидается, что выборка c ограничением по полу height меньше или равно 181 вернет objectsSet из 3 объектов'
        );
    }

    public function testSelectorWhereMoreOrEquals()
    {
        $this->selector->where('height')
            ->equalsOrMore(181);
        $result = $this->selector->getResult();
        $this->assertCount(
            2,
            $result->fetchAll(),
            'Ожидается, что выборка c ограничением по полу height больше или равно 181 вернет objectsSet из 2 объектов'
        );
    }

    public function testSelectorWhereIsNull()
    {
        $this->selector->where('height')
            ->isNull();
        $result = $this->selector->getResult();
        $this->assertCount(
            1,
            $result->fetchAll(),
            'Ожидается, что выборка c ограничением по полу height равному NULL вернет objectsSet из 1 объект'
        );
    }

    public function testSelectorWhereIsNotNull()
    {
        $this->selector->where('height')
            ->notNull();
        $result = $this->selector->getResult();
        $this->assertCount(
            4,
            $result->fetchAll(),
            'Ожидается, что выборка c ограничением по полу height неравному NULL вернет objectsSet из 4 объекта'
        );
    }

    public function testSelectorWhereBetween()
    {
        $this->selector->where('height')
            ->between(167, 170);
        $result = $this->selector->getResult();
        $this->assertCount(
            2,
            $result->fetchAll(),
            'Ожидается, что выборка c ограничением по полу height между 167 и 170  вернет objectsSet из 2 объекта'
        );
    }

    public function testSelectorWhereIn()
    {
        $this->selector->where('height')
            ->in(array(167, 183, 181));
        $result = $this->selector->getResult();
        $this->assertCount(
            3,
            $result->fetchAll(),
            'Ожидается, что выборка c ограничением по полу height со значениями 167, 183 и 181 вернет objectsSet из 3x объектов'
        );
    }

    public function testSelectorWhereLike()
    {
        $this->selector->where('login')
            ->like('%user');
        $result = $this->selector->getResult();
        $this->assertCount(
            2,
            $result->fetchAll(),
            'Ожидается, что выборка c ограничением по полу login вернет objectsSet из 2 объектов'
        );
    }

    public function testSelectorDifficultWhere()
    {
        $this->selector
            ->begin()
            ->where('rating')
            ->more(7)
            ->where('isActive')
            ->between(0, 1)
            ->begin('OR')
            ->where('height')
            ->notEquals(170)
            ->where('height')
            ->isNull()
            ->end()
            ->end();

        $result = $this->selector->getResult();
        $this->assertCount(2, $result->fetchAll(), 'Ожидается, что сложная выборка вернет objectsSet из 2х объектов');
    }

    public function testSelectorDifficultType()
    {
        $this->selector->types(['supervisor'])
            ->where('height')
            ->more(168);
        $result = $this->selector->getResult();
        $this->assertCount(
            1,
            $result->fetchAll(),
            'Ожидается, что выборка c указанием типа supervisor и роста больше 168 вернет objectsSet из 1 объекта'
        );
    }

    public function testSelectorWrongFields()
    {
        $this->selector->where('supervisorField')
            ->isNull();
        $result = $this->selector->getResult();
        $this->assertCount(
            2,
            $result->fetchAll(),
            'Ожидается, что выборка c указанием supervisor_field is null вернет objectsSet из 2 объекта, так как поле supervisor_field присутствует только в типе supervisor'
        );
    }

    public function testSelectorOrderByWrongFields()
    {
        $this->selector->orderBy('supervisorField');
        $result = $this->selector->getResult();
        $this->assertCount(
            2,
            $result->fetchAll(),
            'Ожидается, что выборка c указанием order by supervisor_field вернет objectsSet из 2 объекта, так как поле supervisor_field присутствует только в типе supervisor'
        );
    }

    public function testGetIterator()
    {
        $this->selector->where('height')
            ->equalsOrMore(181);
        $i = 2;
        /**
         * @var IObject $object
         */
        foreach ($this->selector as $object) {
            $i++;
            $this->assertEquals(
                $i,
                $object->getId(),
                'Ожидается, что при итерации селектора будет итерироваться его ObjectSet'
            );
        }

        $this->assertEquals(4, $i, 'Ожидается, что итерация селектора остановилась на пользователи с id 4');
    }

    public function testResetWhere()
    {
        $this->selector->where('height')
            ->in([167, 183, 181]);
        $result = $this->selector->getResult();
        $this->assertCount(
            3,
            $result->fetchAll(),
            'Ожидается, что выборка c ограничением по полу height со значениями 167, 183 и 181 вернет objectsSet из 3x объектов'
        );

        $this->selector->where('height')
            ->in([167, 183]);
        $this->assertCount(
            3,
            $result->fetchAll(),
            'Ожидается, что без ресета выборка вернет уже полученные результаты несмотря на изменение условий'
        );

        $this->selector->resetResult();
        $this->selector->where('height')
            ->in([167, 183]);
        $this->assertCount(2, $result->fetchAll(), 'Ожидается, что выборка учтет изменения, если был применен reset');

        $this->selector->limit(1);
        $this->assertCount(
            2,
            $result->fetchAll(),
            'Ожидается, что без ресета выборка вернет уже полученные результаты несмотря на изменение условий'
        );

        $this->selector->resetResult();
        $this->assertCount(1, $result->fetchAll(), 'Ожидается, что выборка учтет изменения, если был применен reset');

    }

    public function testResetTypes()
    {
        $this->selector->orderBy('supervisorField');
        $result = $this->selector->getResult();
        $this->assertCount(
            2,
            $result->fetchAll(),
            'Ожидается, что выборка c указанием order by supervisor_field вернет objectsSet из 2 объекта, так как поле supervisor_field присутствует только в типе supervisor'
        );

        $this->selector->types(['guest']);
        $e = null;
        try {
            $result->fetchAll();
        } catch (\Exception $e) {
        }
        $this->assertNull(
            $e,
            'Ожидается, что без ресета выборка вернет уже полученные результаты несмотря на изменение условий'
        );

        $this->selector->resetResult();
        try {
            $result->fetchAll();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение после того, как результат выборки был сброшен, и применились невозможные уловия'
        );
    }

    public function testGetTotal()
    {
        $this->assertEquals(
            5,
            $this->selector->result()
                ->count(),
            'Ожидается, что всего 5 объектов удовлетворяют выборке'
        );
        $this->assertEquals(5, $this->selector->getTotal(), 'Ожидается, что всего 5 объектов удовлетворяют выборке');

        $this->selector->resetResult();

        $this->selector->limit(3);
        $this->assertEquals(
            3,
            $this->selector->result()
                ->count(),
            'Ожидается, что всего 3 объектов удовлетворяют выборке c limit'
        );
        $this->assertEquals(
            5,
            $this->selector->getTotal(),
            'Ожидается, что всего 5 объектов удовлетворяют выборке, несмотря на лимит'
        );

        $this->collectionManager->getCollection(self::USERS_USER)
            ->add();
        $this->objectPersister->commit();

        $this->assertEquals(
            5,
            $this->selector->getTotal(),
            'Ожидается, что общее количество закешировалось несмотря на то, что были добавлены новые объекты'
        );

    }

    public function _testSelector()
    {
        /**
         * @var ISelector $selector
         */
        //$selector = $usersCollection->select('user');
        /**
         * order
         * order.customer (customer_id)
         * order.items
         */
        $sql = <<<EOF
	SELECT
		`result`.`id` as `emarket.order.id`,
		`result`.`name` as `emarket.order.name`,
		`result`.`user_name` as `emarket.user.login`,
		`result`.`user_id` as `emarket.user.id`,
		`emarket.order_items`.`id` as `emarket.order_items.id`,
		`emarket.order_items`.`name` as `emarket.order_items.name`
	FROM
		(
			SELECT
					`emarket.order`.`id` as `id`,
					`emarket.order`.`name` as `name`,
					`emarket.user`.`login` as `user_name`,
					`emarket.user`.`id` as `user_id`
				FROM
					`umi_mock_orders` as `emarket.order`
				INNER JOIN `umi_mock_users` as `emarket.user` ON (`emarket.order`.`owner_id` = `emarket.user`.`id`)
				WHERE `emarket.order`.`id` > 6
				ORDER BY `emarket.order`.`id`
				LIMIT 5
		) `result`,
		`umi_mock_order_items` as `emarket.order_items`
	WHERE `emarket.order_items`.`order_id` = `result`.`id`

EOF;

    }

}
