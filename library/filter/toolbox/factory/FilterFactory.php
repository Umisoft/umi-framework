<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\filter\toolbox\factory;

use umi\filter\exception\OutOfBoundsException;
use umi\filter\IFilterFactory;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика фильтров.
 */
class FilterFactory implements IFilterFactory, IFactory
{

    use TFactory;

    /**
     * @var string $filterCollectionClass класс коллекции фильтров
     */
    public $filterCollectionClass = 'umi\filter\FilterCollection';
    /**
     * @var array $types поддерживаемые фильтры
     */
    public $types = array(
        self::TYPE_BOOLEAN         => 'umi\filter\type\Boolean',
        self::TYPE_HTML_ENTITIES   => 'umi\filter\type\HtmlEntities',
        self::TYPE_INT             => 'umi\filter\type\Int',
        self::TYPE_NULL            => 'umi\filter\type\Null',
        self::TYPE_REGEXP          => 'umi\filter\type\Regexp',
        self::TYPE_STRING_TO_LOWER => 'umi\filter\type\StringToLower',
        self::TYPE_STRING_TO_UPPER => 'umi\filter\type\StringToUpper',
        self::TYPE_STRING_TRIM     => 'umi\filter\type\StringTrim',
        self::TYPE_STRIP_NEW_LINES => 'umi\filter\type\StripNewLines',
        self::TYPE_STRIP_TAGS      => 'umi\filter\type\StripTags',

    );

    /**
     * {@inheritdoc}
     */
    public function createFilterCollection(array $config)
    {
        $filters = [];
        foreach ($config as $type => $options) {
            $filters[$type] = $this->createFilter($type, $options);
        }

        return $this->getPrototype(
                $this->filterCollectionClass,
                ['umi\filter\IFilterCollection']
            )
            ->createInstance([$filters]);
    }

    /**
     * {@inheritdoc}
     */
    public function createFilter($type, array $options = [])
    {
        if (!isset($this->types[$type])) {
            throw new OutOfBoundsException($this->translate(
                'Filter "{type}" is not available.',
                ['type' => $type]
            ));
        }

        return $this->getPrototype(
                $this->types[$type],
                ['umi\filter\IFilter']
            )
            ->createInstance([$options]);
    }
}
