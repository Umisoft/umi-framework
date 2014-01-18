<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\toolbox;

use umi\templating\engine\ITemplateEngineAware;
use umi\templating\engine\ITemplateEngineFactory;
use umi\toolkit\exception\UnsupportedServiceException;
use umi\toolkit\toolbox\IToolbox;
use umi\toolkit\toolbox\TToolbox;

/**
 * Инструменты для работы с шаблонизаторами.
 */
class TemplatingTools implements IToolbox
{

    /** Имя набора инструментов */
    const NAME = 'templating';

    use TToolbox;

    /**
     * @var string $templatingFactoryClass класс фабрики шаблонизаторов
     */
    public $templateEngineFactoryClass = 'umi\templating\toolbox\factory\TemplateEngineFactory';

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->registerFactory(
            'engine',
            $this->templateEngineFactoryClass,
            ['umi\templating\engine\ITemplateEngineFactory']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getService($serviceInterfaceName, $concreteClassName)
    {
        switch ($serviceInterfaceName) {
            case 'umi\templating\engine\ITemplateEngineFactory':
                return $this->getTemplateEngineFactory();
        }

        throw new UnsupportedServiceException($this->translate(
            'Toolbox "{name}" does not support service "{interface}".',
            ['name' => self::NAME, 'interface' => $serviceInterfaceName]
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function injectDependencies($object)
    {
        if ($object instanceof ITemplateEngineAware) {
            $object->setTemplateEngineFactory($this->getTemplateEngineFactory());
        }
    }

    /**
     * Возвращает фабрику для шаблонизаторов.
     * @return ITemplateEngineFactory
     */
    protected function getTemplateEngineFactory()
    {
        return $this->getFactory('engine');
    }
}
 