<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\controller\type;

use umi\hmvc\component\request\IComponentRequest;

/**
 * Абстрактный базовый класс контроллера статических страниц.
 */
abstract class StaticPageController extends BaseController
{
    /**
     * @var string $template выбранный шаблон страницы
     */
    protected $template;

    /**
     * {@inheritdoc}
     */
    public function __invoke(IComponentRequest $request)
    {
        return $this->createControllerResult($this->template);
    }
}