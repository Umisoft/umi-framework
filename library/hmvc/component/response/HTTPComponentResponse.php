<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\component\response;

use umi\hmvc\component\IComponent;
use umi\http\response\Response;

/**
 * Реузльтат работы контроллера.
 */
class HTTPComponentResponse extends Response implements IHTTPComponentResponse
{

    /**
     * @var bool $isProcessable статус доступности для обработки
     */
    protected $isProcessable = true;
    /**
     * @var IComponent $component
     */
    private $component;

    /**
     * Конструктор.
     * @param IComponent $component
     */
    public function __construct(IComponent $component)
    {
        $this->component = $component;
    }

    /**
     * {@inheritdoc}
     */
    public function stopProcessing()
    {
        $this->isProcessable = false;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isProcessable()
    {
        return $this->isProcessable;
    }

    /**
     * {@inheritdoc}
     */
    public function getComponent()
    {
        return $this->component;
    }
}