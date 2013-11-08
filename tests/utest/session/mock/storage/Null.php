<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\session\mock\storage;

/**
 * Мок-класс NullStorage
 */
class Null implements \SessionHandlerInterface
{

    protected $actions = [];

    private $data = [];

    /**
     * @return array получить действия совершенные со Storage
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        $this->actions[] = 'close';
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId)
    {
        unset($this->data[$sessionId]);
        $this->actions[] = 'destroy';
    }

    /**
     * {@inheritdoc}
     */
    public function gc($maxlifetime)
    {
        $this->actions[] = 'gc';
    }

    /**
     * {@inheritdoc}
     */
    public function open($savePath, $sessionId)
    {
        $this->actions[] = 'open';
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessionId)
    {
        $this->actions[] = 'read';

        return isset($this->data[$sessionId]) ? $this->data[$sessionId] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, $sessionData)
    {
        $this->data[$sessionId] = $sessionData;
        $this->actions[] = 'write';
    }
}