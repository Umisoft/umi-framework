<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\toolbox\factory;

use umi\templating\extension\helper\IHelperFactory;
use umi\templating\extension\helper\IHelperFactoryAware;
use umi\templating\extension\IExtensionFactory;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика для создания коллекции помощников для шаблонов.
 */
class ExtensionFactory implements IExtensionFactory, IFactory
{
    use TFactory;

    /**
     * @var string $helperCollectionClass класс коллекции помощников для шаблонов
     */
    public $helperCollectionClass = 'umi\templating\extension\helper\collection\HelperCollection';
    /**
     * @var array $helperCollection помощники для шаблонов
     */
    public $helperCollection = [
        'translate'  => 'umi\templating\extension\helper\type\TranslateHelper',
        'form'       => 'umi\templating\extension\helper\type\form\FormHelper',
        'headMeta'   => 'umi\templating\extension\helper\type\head\meta\MetaHelper',
        'headScript' => 'umi\templating\extension\helper\type\head\script\ScriptHelper',
        'headStyle'  => 'umi\templating\extension\helper\type\head\style\StyleHelper',

        'paginatorSliding'  => 'umi\templating\extension\helper\type\paginator\PaginatorSlidingHelper',
        'paginatorElastic'  => 'umi\templating\extension\helper\type\paginator\PaginatorElasticHelper',
        'paginatorJumping'  => 'umi\templating\extension\helper\type\paginator\PaginatorJumpingHelper',
        'paginatorAll'  => 'umi\templating\extension\helper\type\paginator\PaginatorAllHelper',
    ];
    /**
     * @var string $helperFactoryClass класс фабрики для создания помощников для шаблонов
     */
    public $helperFactoryClass = 'umi\templating\toolbox\factory\HelperFactory';

    /**
     * {@inheritdoc}
     */
    public function createHelperCollection()
    {
        $helperCollection = $this->createInstance(
            $this->helperCollectionClass,
            [],
            ['umi\templating\extension\helper\collection\IHelperCollection']
        );

        if ($helperCollection instanceof IHelperFactoryAware) {
            $helperCollection->setTemplatingHelperFactory($this->getHelperFactory());
        }

        return $helperCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultHelperCollection()
    {
        return $this->createHelperCollection()
            ->addHelpers($this->helperCollection);
    }

    /**
     * Возвращает фабрику помощников шаблонов.
     * @return IHelperFactory
     */
    protected function getHelperFactory()
    {
        return $this->createSingleInstance(
            $this->helperFactoryClass,
            [],
            ['umi\templating\extension\helper\IHelperFactory']
        );
    }
}