<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\session\entity\ns;

use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\session\exception\RuntimeException;
use umi\session\ISessionManagerAware;
use umi\session\TSessionManagerAware;
use umi\spl\container\TArrayAccess;

/**
 * Класс пространства имен сессии.
 * Контейнер значений пространства имен сессии. Запускает сессию при необходимости.
 */
class SessionNamespace implements ISessionNamespace, ISessionManagerAware, ILocalizable
{

    use TArrayAccess;
    use TSessionManagerAware;
    use TLocalizable;

    /**
     * @var array $container контейнер значений пространства имен
     */
    protected $container = [];
    /**
     * @var array $metadata метаданные сессии
     */
    protected $metadata = [];
    /**
     * @var string $name пространство имен сессии
     */
    protected $name;

    /**
     * Конструктор. Автоматически запускает сессию, если она существует
     * @param string $name имя сессии
     * @throws RuntimeException если имя не передано
     */
    public function __construct($name)
    {
        if (!$name) {
            throw new RuntimeException($this->translate(
                'Cannot create session namespace without name.'
            ));
        }
        $this->name = $name;

        $this->readFromSession();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function get($attribute)
    {
        return isset($this->container[$attribute]) ? $this->container[$attribute] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function set($attribute, $value)
    {
        $this->sessionStart();
        $this->container[$attribute] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function has($attribute)
    {
        return isset($this->container[$attribute]);
    }

    /**
     * {@inheritdoc}
     */
    public function del($attribute)
    {
        if ($this->has($attribute)) {
            $this->sessionStart();
            unset($this->container[$attribute]);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->container = [];
        $this->metadata = [];
        $this->readFromSession();
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->container;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($key)
    {
        return isset($this->metadata[$key]) ? $this->metadata[$key] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetadata($key, $value)
    {
        $this->metadata[$key] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->container);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return current($this->container);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        return next($this->container);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return key($this->container);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return key($this->container) !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        return reset($this->container);
    }

    protected function sessionStart()
    {
        $this->startSession();
        $this->writeToSession();
    }

    protected function readFromSession()
    {
        if (!isset($_SESSION[$this->name])) {
            $_SESSION[$this->name] = [
                'meta'   => [],
                'values' => []
            ];
        }

        $this->container = & $_SESSION[$this->name]['values'];
        $this->metadata = & $_SESSION[$this->name]['meta'];
    }

    protected function writeToSession()
    {
        $_SESSION[$this->name] = [
            'meta'   => &$this->metadata,
            'values' => &$this->container
        ];
    }
}