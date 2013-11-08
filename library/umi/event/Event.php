<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\event;

/**
 * Событие.
 */
class Event implements IEvent
{
    /**
     * @var bool $isPropagationStopped флаг остановки цепочки событий
     */
    protected $isPropagationStopped = false;
    /**
     * @var mixed $target объект, в котором произошло событие
     */
    protected $target;
    /**
     * @var string $type тип события
     */
    protected $type;
    /**
     * @var array $params список параметров события
     */
    protected $params = [];
    /**
     * @var array $tags список тэгов, с которыми произошло событие
     */
    protected $tags = [];

    /**
     * Конструктор события.
     * @param string $type тип события
     * @param mixed $target объект, в котором произошло событие
     * @param array $params список параметров события array('paramName' => 'paramVal', 'relParam' => &$var)
     * @param array $tags список тэгов, с которыми произошло событие
     */
    public function __construct($type, $target, array $params = [], array $tags = [])
    {
        $this->type = $type;
        $this->target = $target;
        $this->params = $params;
        $this->tags = $tags;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * {@inheritdoc}
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * {@inheritdoc}
     */
    public function stopPropagation($stopped = true)
    {
        $this->isPropagationStopped = (bool) $stopped;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPropagationIsStopped()
    {
        return $this->isPropagationStopped;
    }

    /**
     * {@inheritdoc}
     */
    public function &getParam($name)
    {
        $val =& $this->params[$name];

        return $val;
    }

    /**
     * {@inheritdoc}
     */
    public function getParams()
    {
        return $this->params;
    }
}
