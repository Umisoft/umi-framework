<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest;

use ReflectionClass;
use ReflectionProperty;
use umi\toolkit\factory\IFactory;
use umi\toolkit\IToolkit;
use umi\toolkit\prototype\IPrototypeFactory;
use umi\toolkit\prototype\PrototypeFactory;
use umi\toolkit\Toolkit;

/**
 * Базовый класс тест-кейса UMI.
 * Автоматически чистит фикстуры и используемые БД
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IToolkit $toolkit
     */
    private $toolkit;
    /**
     * @var IPrototypeFactory $prototypeFactory
     */
    private $prototypeFactory;

    /**
     * Общий метод установки окружения, переопределять нельзя.
     * Необходимо перегрузить setUpFixtures для конкретного тест кейса
     */
    protected function setUp()
    {
        $this->setUpFixtures();
    }

    /**
     * Метод для создания специфического окружения тест-кейса.
     * Может быть перегружен в конкретном тест-кейсе, если это необходимо
     */
    protected function setUpFixtures()
    {
    }

    /**
     * Общий метод очистки окружения, переопределять нельзя.
     * Необходимо перегрузить tearDownFixtures для конкретного тест кейса
     */
    protected function tearDown()
    {
        $this->tearDownFixtures();

        $this->clearFixtureProperties();
    }

    /**
     * Метод для очистки специфического окружения тест-кейса.
     * Может быть перегружен в конкретном тест-кейсе, если это необходимо
     */
    protected function tearDownFixtures()
    {
    }

    /**
     * Получить тестовый тулкит
     * @throws \RuntimeException
     * @return IToolkit
     */
    protected function getTestToolkit()
    {
        if (!$this->toolkit) {
            $this->toolkit = new Toolkit();
            $this->prototypeFactory = new PrototypeFactory($this->toolkit);
            $this->toolkit->setPrototypeFactory($this->prototypeFactory);
        }

        return $this->toolkit;
    }

    /**
     * Разрешает опциональные зависимости для тестируемого объекта
     * @param mixed $object объект
     */
    protected function resolveOptionalDependencies($object)
    {
        $testToolkit = $this->getTestToolkit();
        $prototype = $this->prototypeFactory->create(get_class($object));
        $prototype->resolveDependencies();

        if ($object instanceof IFactory) {
            $object->setToolkit($testToolkit);
            $object->setPrototypeFactory($this->prototypeFactory);
        }

        $prototype->wakeUpInstance($object);
    }

    /**
     * Обходит все свойства тест-кейса и очищает известные фикстуры
     */
    protected function clearFixtureProperties()
    {
        $reflection = new \ReflectionClass($this);

        $this->toolkit = null;
        $this->prototypeFactory = null;

        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($this);
            $name = $property->getName();
            if (is_string($value) && strpos($name, 'fs') === 0) {
                $property->setValue($this, null);
                $this->clearFsFixture($value);
            } elseif (is_object($value)) {
                $property->setValue($this, null);
            }

        }
    }

    /**
     * Рекурсивно удаляет объекты файловой системы (директорию, файл)
     * @param string $path путь к объекту fs
     * @return bool
     */
    protected function clearFsFixture($path)
    {
        @chmod($path, 0777);
        if (is_file($path)) {
            unlink($path);

            return true;
        }
        if (is_dir($path)) {
            foreach (scandir($path) as $file) {
                if (!in_array($file, array('.', '..'))) {
                    $this->clearFsFixture($path . DIRECTORY_SEPARATOR . $file);
                }
            }

            return rmdir($path);
        }

        return false;
    }

}