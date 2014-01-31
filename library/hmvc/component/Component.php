<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\component;

use umi\acl\IACLAware;
use umi\acl\manager\IACLManager;
use umi\acl\TACLAware;
use umi\hmvc\controller\IControllerFactory;
use umi\hmvc\exception\OutOfBoundsException;
use umi\hmvc\IMVCEntityFactoryAware;
use umi\hmvc\macros\IMacrosFactory;
use umi\hmvc\model\IModelAware;
use umi\hmvc\model\IModelFactory;
use umi\hmvc\TMVCEntityFactoryAware;
use umi\hmvc\view\IViewRenderer;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\route\IRouteAware;
use umi\route\IRouter;
use umi\route\TRouteAware;
use umi\spl\config\TConfigSupport;

/**
 * Реализация MVC компонента системы.
 */
class Component implements IComponent, IMVCEntityFactoryAware, IRouteAware, ILocalizable, IACLAware
{
    use TMVCEntityFactoryAware;
    use TRouteAware;
    use TLocalizable;
    use TConfigSupport;
    use TACLAware;

    /**
     * @var string $path иерархический путь компонента
     */
    protected $path;
    /**
     * @var string $name имя компонента
     */
    protected $name;
    /**
     * @var array $options опции компонента
     */
    protected $options;
    /**
     * @var IComponent[] $children дочерние компоненты
     */
    private $children = [];
    /**
     * @var IRouter $router роутер компонента
     */
    private $router;
    /**
     * @var IControllerFactory $controllerFactory фабрика контроллеров
     */
    private $controllerFactory;
    /**
     * @var IMacrosFactory $macrosFactory фабрика макросов
     */
    private $macrosFactory;
    /**
     * @var IModelFactory $modelFactory фабрика моделей
     */
    private $modelFactory;
    /**
     * @var IViewRenderer $viewRenderer рендерер шаблонов
     */
    private $viewRenderer;
    /**
     * @var IACLManager $aclManager менеджер ACL
     */
    private $aclManager;

    /**
     * Конструктор.
     * @param string $name имя компонента
     * @param string $path иерархический путь компонента
     * @param array $options опции
     */
    public function __construct($name, $path, array $options = [])
    {
        $this->name = $name;
        $this->path = $path;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildComponent($name)
    {
        return isset($this->options[self::OPTION_COMPONENTS][$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getChildComponent($name)
    {
        if (isset($this->children[$name])) {
            return $this->children[$name];
        }

        if (!$this->hasChildComponent($name)) {
            throw new OutOfBoundsException($this->translate(
                'Cannot create child component "{name}". Component has not registered.',
                ['name' => $name]
            ));
        }

        $config = $this->configToArray($this->options[self::OPTION_COMPONENTS][$name]);
        $component = $this->createMVCComponent($name, $this->path . self::PATH_SEPARATOR . $name, $config);

        return $this->children[$name] = $component;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouter()
    {
        if (!$this->router) {
            $config = isset($this->options[self::OPTION_ROUTES]) ? $this->options[self::OPTION_ROUTES] : [];
            $config = $this->configToArray($config, true);

            return $this->router = $this->createRouter($config);
        }

        return $this->router;
    }

    /**
     * {@inheritdoc}
     */
    public function hasController($controllerName)
    {
        return $this->getControllerFactory()->hasController($controllerName);
    }

    /**
     * {@inheritdoc}
     */
    public function getController($controllerName, array $args = [])
    {
        return $this->getControllerFactory()->createController($controllerName, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function hasMacros($macrosName)
    {
        return $this->getMacrosFactory()->hasMacros($macrosName);
    }

    /**
     * {@inheritdoc}
     */
    public function getMacros($macrosName, array $params = [])
    {
        return $this->getMacrosFactory()->createMacros($macrosName, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function getViewRenderer()
    {
        if (!$this->viewRenderer) {
            $config = isset($this->options[self::OPTION_VIEW]) ? $this->options[self::OPTION_VIEW] : [];
            $config = $this->configToArray($config, true);

            $viewRenderer = $this->createMVCViewRenderer($config);

            if ($viewRenderer instanceof IModelAware) {
                $viewRenderer->setModelFactory($this->getModelsFactory());
            }

            return $this->viewRenderer = $viewRenderer;
        }

        return $this->viewRenderer;
    }

    /**
     * {@inheritdoc}
     */
    public function getACLManager()
    {
        if (!$this->aclManager) {

            $config = isset($this->options[self::OPTION_ACL]) ? $this->options[self::OPTION_ACL] : [];
            $config = $this->configToArray($config, true);

            $this->aclManager = $this->getACLFactory()->createACLManager($config);
        }

        return $this->aclManager;
    }

    /**
     * Возвращает фабрику контроллеров компонента.
     * @return IControllerFactory
     */
    protected function getControllerFactory()
    {
        if (!$this->controllerFactory) {
            $controllerList = isset($this->options[self::OPTION_CONTROLLERS]) ? $this->options[self::OPTION_CONTROLLERS] : [];
            $controllerList = $this->configToArray($controllerList, true);

            $controllerFactory = $this->createMVCControllerFactory($this, $controllerList);

            if ($controllerFactory instanceof IModelAware) {
                $controllerFactory->setModelFactory($this->getModelsFactory());
            }

            return $this->controllerFactory = $controllerFactory;
        }

        return $this->controllerFactory;
    }

    /**
     * Возвращает фабрику макросов компонента.
     * @return IMacrosFactory
     */
    protected function getMacrosFactory()
    {
        if (!$this->macrosFactory) {
            $macrosList = isset($this->options[self::OPTION_MACROS]) ? $this->options[self::OPTION_MACROS] : [];
            $macrosList = $this->configToArray($macrosList, true);

            $macrosFactory = $this->createMVCMacrosFactory($this, $macrosList);

            if ($macrosFactory instanceof IModelAware) {
                $macrosFactory->setModelFactory($this->getModelsFactory());
            }

            return $this->macrosFactory = $macrosFactory;
        }

        return $this->macrosFactory;
    }

    /**
     * Возвращает фабрику моделей компонента.
     * @return IModelFactory
     */
    protected function getModelsFactory()
    {
        if (!$this->modelFactory) {
            $config = isset($this->options[self::OPTION_MODELS]) ? $this->options[self::OPTION_MODELS] : [];
            $config = $this->configToArray($config, true);

            return $this->modelFactory = $this->createMVCModelFactory($config);
        }

        return $this->modelFactory;
    }

}
