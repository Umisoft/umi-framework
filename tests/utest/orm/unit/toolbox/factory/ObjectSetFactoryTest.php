<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\toolbox\factory;

use umi\orm\metadata\field\relation\ManyToManyRelationField;
use umi\orm\toolbox\factory\ObjectFactory;
use umi\orm\toolbox\factory\ObjectSetFactory;
use umi\orm\toolbox\factory\PropertyFactory;
use utest\orm\ORMTestCase;

/**
 * Тест фабрики набора объектов
 */
class ObjectSetFactoryTest extends ORMTestCase
{

    /**
     * @var ObjectFactory $objectFactory
     */
    protected $objectFactory;

    /**
     * @var ObjectSetFactory $objectSetFactory
     */
    protected $objectSetFactory;

    /**
     * {@inheritdoc}
     */
    protected function getCollections()
    {
        return [];
    }

    protected function setUpFixtures()
    {
        $this->objectFactory = new ObjectFactory(new PropertyFactory);
        $this->resolveOptionalDependencies($this->objectFactory);

        $this->objectSetFactory = new ObjectSetFactory();
        $this->resolveOptionalDependencies($this->objectSetFactory);
    }

    public function testObjectSetCreation()
    {

        $this->assertInstanceOf(
            'umi\orm\objectset\IObjectSet',
            $this->objectSetFactory->createObjectSet(),
            'Ожидается, что IObjectFactory::createObjectSet() вернет IObjectSet'
        );
        $this->assertInstanceOf(
            'umi\orm\objectset\IEmptyObjectSet',
            $this->objectSetFactory->createEmptyObjectSet(),
            'Ожидается, что IObjectFactory::createEmptyObjectSet() вернет IEmptyObjectSet'
        );

        $blogMetadata = $this->metadataManager->getMetadata(self::BLOGS_BLOG);
        $blog = $this->objectFactory->createObject(
            $this->collectionManager->getCollection(self::BLOGS_BLOG),
            $blogMetadata->getBaseType()
        );
        /**
         * @var ManyToManyRelationField $subscribersField
         */
        $subscribersField = $blogMetadata->getField('subscribers');

        $manyToManyObjectSet = $this->objectSetFactory->createManyToManyObjectSet($blog, $subscribersField);
        $this->assertInstanceOf(
            'umi\orm\objectset\IManyToManyObjectSet',
            $manyToManyObjectSet,
            'Ожидается, что IObjectFactory::createManyToManyObjectSet() вернет IManyToManyObjectSet'
        );
    }
}
