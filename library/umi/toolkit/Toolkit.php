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
        return isset($this->registeredToolboxes[$toolboxName]);
    }

    /**
     * {@inheritdoc}
     */
    public function registerToolbox($toolboxConfig)
    {
        try {
            $toolboxConfig = $this->configToArray($toolboxConfig, true);
        } catch (\InvalidArgumentException $e) {
            throw new UnexpectedValueException($this->translate(
                'Cannot register toolbox. Invalid configuration.'
            ), 0, $e);
        }

        $toolboxName = $this->getRequiredOption(
            $toolboxConfig,
            'name',
            function () {
                throw new InvalidArgumentException($this->translate(
                    'Cannot register toolbox. Option "name" required.'
                ));
            }
        );

        $toolboxClass = $this->getRequiredOption(
            $toolboxConfig,
            'class',
            function () use ($toolboxName) {
                throw new InvalidArgumentException($this->translate(
                    'Cannot register toolbox "{name}". Option "class" required.',
                    ['name' => $toolboxName]
                ));
            }
        );

        $this->trace(
            'Registering toolbox "{name}" with class "{class}".',
            ['name' => $toolboxName, 'class' => $toolboxClass]
        );

        $servicingInterfaces = [];
        if (isset($toolboxConfig['servicingInterfaces']) && is_array($toolboxConfig['servicingInterfaces'])) {
            $servicingInterfaces = $toolboxConfig['servicingInterfaces'];
        }

        $services = [];
        if (isset($toolboxConfig['services']) && is_array($toolboxConfig['services'])) {
            $services = $toolboxConfig['services'];
        }

        if ($this->hasToolbox($toolboxName)) {
            throw new AlreadyRegisteredException($this->translate(
                'Toolbox "{name}" already registered.',
                ['name' => $toolboxName]
            ));
        }

        $this->registeredToolboxes[$toolboxName] = $toolboxClass;

        $this->registerToolboxServices($toolboxName, $services);
        $this->registerToolboxInjectors($toolboxName, $servicingInterfaces);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function registerToolboxes($config)
    {
        try {
            $config = $this->configToArray($config);
        } catch (\InvalidArgumentException $e) {
            throw new UnexpectedValueException($this->translate(
                'Cannot register toolboxes. Invalid configuration.'
            ), 0, $e);
        }

        foreach ($config as $toolboxConfig) {
            $this->registerToolbox($toolboxConfig);
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
    public function registerService($serviceInterfaceName, callable $builder)
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
            $this->settings[$toolboxName] = $toolboxSettings;
        }

        return $this;
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
    public function getService($serviceInterfaceName, $concreteClassName = null)
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
     * Возвращает экземляр набора инструментов
     * @param string $toolboxName интерфейс набора инструментов, либо алиас
     * @throws NotRegisteredException если набор инструментов не зарегистрирован
     * @throws DomainException если экземпляр набора инструментов не соответсвует интерфейсу
     * @throws RuntimeException если зарегистрированный интерфейс не существует
     * @return object|IToolbox
     */
    protected function getToolbox($toolboxName)
    {
        if (isset($this->toolboxes[$toolboxName])) {
            return $this->toolboxes[$toolboxName];
        }

        $options = $this->getToolboxSettings($toolboxName);
        $toolboxClass = $this->registeredToolboxes[$toolboxName];

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
            $this->toolboxes[$toolboxName] = $toolbox;

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
                'Cannot create toolbox "{name}".',
                ['name' => $toolboxName]
            ), 0, $e);
        }

        return $this->toolboxes[$toolboxName];
    }

    /**
     * Регистрирует интерфейсы сервисов, которые умеет обслуживать указанный набор инструментов
     * @param string $toolboxName набор инструментов
     * @param array $services конфигурация сервисов
     * @throws AlreadyRegisteredException если какой-либо из сервисов был зарегистрирован ранее
     * @throws InvalidArgumentException если конфигурация сервиса не верна
     */
    protected function registerToolboxServices($toolboxName, array $services)
    {
        foreach ($services as $serviceInterfaceName) {
            $this->registerService(
                $serviceInterfaceName,
                function ($concreteClassName = null) use ($toolboxName, $serviceInterfaceName) {
                    return $this->getToolbox($toolboxName)
                        ->getService($serviceInterfaceName, $concreteClassName);
                }
            );
        }
    }

    /**
     * Регистрирует интерфейсы, которые умеет обслуживать указанный набор инструментов
     * @param string $toolboxName набор инструментов
     * @param array $servicingInterfaces конфигурация публично доступных aware-интерфейсов
     * @throws AlreadyRegisteredException если какой-либо из интерфейсов был зарегистрирован ранее
     * @throws InvalidArgumentException если конфигурация интерфейса не верна
     */
    protected function registerToolboxInjectors($toolboxName, array $servicingInterfaces)
    {
        $injector = function ($object) use ($toolboxName) {
            $this->trace(
                'Inject dependencies in "{class}", using toolbox "{name}".',
                [
                    'class' => get_class($object),
                    'name'  => $toolboxName
                ]
            );
            $this->getToolbox($toolboxName)
                ->injectDependencies($object);
        };

        foreach ($servicingInterfaces as $interface) {
            $this->registerInjector($interface, $injector);
        }
    }

    /**
     * Возвращает настройки набора инструментов.
     * @param string $toolboxName имя набора инструментов
     * @return array|Traversable
     */
    protected function getToolboxSettings($toolboxName)
    {
        if (isset($this->settings[$toolboxName])) {
            return $this->settings[$toolboxName];
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