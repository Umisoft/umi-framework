<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\toolkit;

use Traversable;
use umi\event\toolbox\TEventObservant;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\log\ILoggerAware;
use umi\log\TLoggerAware;
use umi\spl\config\TConfigSupport;
use umi\toolkit\exception\AlreadyRegisteredException;
use umi\toolkit\exception\DomainException;
use umi\toolkit\exception\InvalidArgumentException;
use umi\toolkit\exception\NotRegisteredException;
use umi\toolkit\exception\RuntimeException;
use umi\toolkit\exception\UnexpectedValueException;
use umi\toolkit\factory\IFactory;
use umi\toolkit\prototype\IPrototype;
use umi\toolkit\prototype\IPrototypeAware;
use umi\toolkit\prototype\IPrototypeFactory;
use umi\toolkit\prototype\PrototypeFactory;
use umi\toolkit\prototype\TPrototypeAware;
use umi\toolkit\toolbox\IToolbox;

/**
 * Тулкит.
 */
class Toolkit implements IToolkit, IPrototypeAware, ILoggerAware, ILocalizable
{
    use TConfigSupport;
    use TLoggerAware;
    use TLocalizable;

    /**
     * @var IPrototypeFactory $_prototypeFactory
     */
    private $prototypeFactory;
    /**
     * @var array $registeredToolboxes список зарегистрированных тулбоксов
     */
    protected $registeredToolboxes = [];
    /**
     * @var array $aliases алиасы для наборов инструментов
     */
    protected $aliases = [];
    /**
     * @var IToolbox[] $toolboxes список созданных экземпляров тулбоксов
     */
    protected $toolboxes = [];
    /**
     * @var callable[] $serviceBuilders билдеры сервисов, поддерживаемые тулкитом
     */
    protected $serviceBuilders = [];
    /**
     * @var callable[] $injectors инжекторы, поддерживаемые тулкитом
     */
    protected $injectors = [];
    /**
     * @var array $settings настройки наборов инстурментов
     */
    protected $settings = [];

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->registerInjector(
            'umi\toolkit\IToolkitAware',
            function (IToolkitAware $object) {
                $object->setToolkit($this);
            }
        );
    }

    /**
     * Устанавливает фабрику для создания прототипов
     * @param IPrototypeFactory $prototypeFactory
     * @return self
     */
    public function setPrototypeFactory(IPrototypeFactory $prototypeFactory)
    {
        $this->prototypeFactory = $prototypeFactory;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasToolbox($toolboxName)
    {
        return !is_null($this->getToolboxInterface($toolboxName));
    }

    /**
     * {@inheritdoc}
     */
    public function registerToolbox(array $toolboxConfig)
    {
        try {
            $toolboxConfig = $this->configToArray($toolboxConfig, true);
        } catch (\InvalidArgumentException $e) {
            throw new UnexpectedValueException($this->translate(
                'Cannot register toolbox. Invalid configuration.'
            ), 0, $e);
        }

        $toolboxInterface = $this->getRequiredOption(
            $toolboxConfig,
            'toolboxInterface',
            function () {
                throw new InvalidArgumentException($this->translate(
                    'Cannot register toolbox. Option "toolboxInterface" required.'
                ));
            }
        );

        $defaultClass = $this->getRequiredOption(
            $toolboxConfig,
            'defaultClass',
            function () use ($toolboxInterface) {
                throw new InvalidArgumentException($this->translate(
                    'Cannot register toolbox "{interface}". Option "defaultClass" required.',
                    ['interface' => $toolboxInterface]
                ));
            }
        );

        $this->trace(
            'Registering toolbox "{toolbox}" with class "{class}".',
            ['toolbox' => $toolboxInterface, 'class' => $defaultClass]
        );

        $servicingInterfaces = [];
        if (isset($toolboxConfig['servicingInterfaces']) && is_array($toolboxConfig['servicingInterfaces'])) {
            $servicingInterfaces = $toolboxConfig['servicingInterfaces'];
        }

        $services = [];
        if (isset($toolboxConfig['services']) && is_array($toolboxConfig['services'])) {
            $services = $toolboxConfig['services'];
        }

        $toolboxAliases = [];
        if (isset($toolboxConfig['aliases']) && is_array($toolboxConfig['aliases'])) {
            $toolboxAliases = $toolboxConfig['aliases'];
        }

        if ($this->hasToolbox($toolboxInterface)) {
            throw new AlreadyRegisteredException($this->translate(
                'Toolbox "{toolbox}" already registered.',
                ['toolbox' => $toolboxInterface]
            ));
        }

        $this->registeredToolboxes[$toolboxInterface] = $defaultClass;

        $this->registerToolboxServices($toolboxInterface, $services);
        $this->registerToolboxInjectors($toolboxInterface, $servicingInterfaces);
        $this->registerToolboxAliases($toolboxInterface, $toolboxAliases);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function registerToolboxes(array $config)
    {
        foreach ($config as $toolboxConfig) {
            if (!is_array($toolboxConfig)) {
                throw new InvalidArgumentException($this->translate(
                    'Cannot register toolbox. Configuration should be an array.'
                ));
            }

            $this->registerToolbox($toolboxConfig);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function registerToolboxAliases($toolboxName, array $aliases)
    {
        $toolboxInterface = $this->getToolboxInterface($toolboxName);
        if (!$toolboxInterface) {
            throw new NotRegisteredException($this->translate(
                'Cannot register aliases. Toolbox "{toolbox}" is not registered.',
                ['toolbox' => $toolboxName]
            ));
        }
        foreach ($aliases as $alias) {
            if (isset($this->aliases[$alias])) {
                throw new AlreadyRegisteredException($this->translate(
                    'Cannot register alias "{alias}" for toolbox "{toolbox}". Alias already registered.',
                    ['alias' => $alias, 'toolbox' => $toolboxName]
                ));
            }
            $this->aliases[$alias] = $toolboxInterface;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasService($serviceInterfaceName)
    {
        return array_key_exists($serviceInterfaceName, $this->serviceBuilders);
    }

    /**
     * {@inheritdoc}
     */
    public function register($serviceInterfaceName, callable $builder)
    {
        if ($this->hasService($serviceInterfaceName)) {
            throw new AlreadyRegisteredException($this->translate(
                'Cannot register service. Builder for service "{service}" already registered.',
                ['service' => $serviceInterfaceName]
            ));
        }
        $this->trace(
            'Registering builder for service "{service}".',
            ['service' => $serviceInterfaceName]
        );
        $this->serviceBuilders[$serviceInterfaceName] = $builder;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasInjector($servicingInterfaceName)
    {
        return array_key_exists($servicingInterfaceName, $this->injectors);
    }

    /**
     * {@inheritdoc}
     */
    public function registerInjector($servicingInterfaceName, callable $injector)
    {
        if ($this->hasInjector($servicingInterfaceName)) {
            throw new AlreadyRegisteredException($this->translate(
                'Cannot register injector. Injector for "{interface}" already registered.',
                ['interface' => $servicingInterfaceName]
            ));
        }
        $this->trace(
            'Registering injector for "{interface}".',
            ['interface' => $servicingInterfaceName]
        );
        $this->injectors[$servicingInterfaceName] = $injector;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSettings($settings)
    {
        try {
            $settings = $this->configToArray($settings);
        } catch (\InvalidArgumentException $e) {
            throw new InvalidArgumentException('Cannot set toolkit settings.', 0, $e);
        }

        foreach ($settings as $toolboxName => $toolboxSettings) {
            try {
                $toolboxSettings = $this->configToArray($toolboxSettings);
            } catch (\InvalidArgumentException $e) {
                throw new UnexpectedValueException($this->translate(
                    'Cannot set toolbox "{toolbox}" settings.',
                    ['toolbox' => $toolboxName]
                ), 0, $e);
            }

            $toolboxInterface = $this->getToolboxInterface($toolboxName);
            $this->settings[$toolboxInterface] = $toolboxSettings;
        }

        return $this;
    }

    /**
     * Возвращает экземляр набора инструментов
     * {@deprecated}
     * @param string $toolboxName интерфейс набора инструментов, либо алиас
     * @throws NotRegisteredException если набор инструментов не зарегистрирован
     * @throws DomainException если экземпляр набора инструментов не соответсвует интерфейсу
     * @throws RuntimeException если зарегистрированный интерфейс не существует
     * @return object|IToolbox
     */
    public function getToolbox($toolboxName)
    {
        $toolboxInterface = $this->getToolboxInterface($toolboxName);
        if (isset($this->toolboxes[$toolboxInterface])) {
            return $this->toolboxes[$toolboxInterface];
        }

        if (!$toolboxInterface) {
            throw new NotRegisteredException($this->translate(
                'Cannot create toolbox "{toolbox}". Toolbox is not registered.',
                ['toolbox' => $toolboxName]
            ));
        }

        $options = $this->getToolboxSettings($toolboxInterface);
        $toolboxClass = $this->registeredToolboxes[$toolboxInterface];

        $this->trace(
            'Creating toolbox "{toolbox}" instance with class "{class}".',
            ['toolbox' => $toolboxName, 'class' => $toolboxClass]
        );

        try {
            /**
             * @var IToolkitAware|IPrototype $prototype
             */
            $prototype = $this->getPrototypeFactory()
                ->create(
                $toolboxClass,
                [
                    'umi\toolkit\toolbox\IToolbox'
                ]
            );

            /**
             * @var IToolbox $toolbox
             */
            $toolbox = $prototype->getPrototypeInstance();
            $this->toolboxes[$toolboxInterface] = $toolbox;

            if ($toolbox instanceof IFactory) {
                $toolbox->setPrototypeFactory($this->getPrototypeFactory());
                $toolbox->setToolkit($this);
            }

            if ($options) {
                $prototype->setOptions($toolbox, $options);
            }

            $prototype->resolveDependencies();
            $prototype->invokeConstructor($toolbox);

        } catch (\Exception $e) {
            throw new RuntimeException($this->translate(
                'Cannot create toolbox "{toolbox}".',
                ['toolbox' => $toolboxInterface]
            ), 0, $e);
        }

        return $this->toolboxes[$toolboxInterface];
    }

    /**
     * {@inheritdoc}
     */
    public function getInjectors(array $interfaceNames)
    {
        $result = [];
        foreach ($interfaceNames as $interface) {
            if (array_key_exists($interface, $this->injectors)) {
                if (!in_array($this->injectors[$interface], $result, true)) {
                    $result[$interface] = $this->injectors[$interface];
                }
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function get($serviceInterfaceName, $concreteClassName = null)
    {
        if (!$this->hasService($serviceInterfaceName)) {
            throw new NotRegisteredException($this->translate(
                'Service {service} is not registered.',
                ['service' => $serviceInterfaceName]
            ));
        }
        $factory = $this->serviceBuilders[$serviceInterfaceName];

        return call_user_func($factory, $concreteClassName, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->toolboxes = [];
    }

    /**
     * {@inheritdoc}
     */
    public function findServiceBuilderByContracts(array $contracts)
    {
        foreach ($contracts as $contact) {
            if ($this->hasService($contact)) {
                return $this->serviceBuilders[$contact];
            }
        }

        return null;
    }

    /**
     * Регистрирует интерфейсы сервисов, которые умеет обслуживать указанный набор инструментов
     * @param string $toolboxInterface набор инструментов
     * @param array $services конфигурация сервисов
     * @throws AlreadyRegisteredException если какой-либо из сервисов был зарегистрирован ранее
     * @throws InvalidArgumentException если конфигурация сервиса не верна
     */
    protected function registerToolboxServices($toolboxInterface, array $services)
    {
        foreach ($services as $serviceInterfaceName) {
            $this->register(
                $serviceInterfaceName,
                function ($concreteClassName = null) use ($toolboxInterface, $serviceInterfaceName) {
                    return $this->getToolbox($toolboxInterface)
                        ->getService($serviceInterfaceName, $concreteClassName);
                }
            );
        }
    }

    /**
     * Регистрирует интерфейсы, которые умеет обслуживать указанный набор инструментов
     * @param string $toolboxInterface набор инструментов
     * @param array $servicingInterfaces конфигурация публично доступных aware-интерфейсов
     * @throws AlreadyRegisteredException если какой-либо из интерфейсов был зарегистрирован ранее
     * @throws InvalidArgumentException если конфигурация интерфейса не верна
     */
    protected function registerToolboxInjectors($toolboxInterface, array $servicingInterfaces)
    {
        $injector = function ($object) use ($toolboxInterface) {
            $this->trace(
                'Inject dependencies in "{class}", using toolbox "{interface}".',
                [
                    'class'     => get_class($object),
                    'interface' => $toolboxInterface
                ]
            );
            $this->getToolbox($toolboxInterface)
                ->injectDependencies($object);
        };

        foreach ($servicingInterfaces as $interface) {
            $this->registerInjector($interface, $injector);
        }
    }

    /**
     * Возвращает имя интерфейса набора инструментов по его алиасу.
     * @param string $toolboxName интерфейс набора инструментов, либо алиас
     * @return string имя интерфейса, либо null, если набор инструментов не зарегистрирован
     */
    protected function getToolboxInterface($toolboxName)
    {
        if (isset($this->registeredToolboxes[$toolboxName])) {
            return $toolboxName;
        }
        if (isset($this->aliases[$toolboxName])) {
            return $this->aliases[$toolboxName];
        }

        return null;
    }

    /**
     * Возвращает настройки набора инструментов.
     * @param string $toolboxInterface имя интерфейса набора инструментов
     * @return array|Traversable
     */
    protected function getToolboxSettings($toolboxInterface)
    {
        if (isset($this->settings[$toolboxInterface])) {
            return $this->settings[$toolboxInterface];
        }

        return [];
    }

    /**
     * Возвращает фабрику прототипов сервисов.
     * @return IPrototypeFactory
     */
    protected function getPrototypeFactory()
    {
        if (!$this->prototypeFactory) {
            $this->prototypeFactory = new PrototypeFactory($this);
        }

        return $this->prototypeFactory;
    }
}