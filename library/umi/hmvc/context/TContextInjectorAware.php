<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\context;

/**
 * Class TContextInjectorAware
 */
trait TContextInjectorAware
{
    use TContextAware;

    /**
     * Внедряет зависимости контекстные зависимости при необходимости.
     * @param object $object
     */
    protected function injectContext($object)
    {
        if ($object instanceof IRequestContext) {
            $object->setContextRequest(
                $this->hasContextRequest() ? $this->getContextRequest() : null
            );
        }

        if ($object instanceof IRouterContext) {
            $object->setContextRouter(
                $this->hasContextComponent() ? $this->getContextComponent()
                    ->getRouter() : null
            );
        }

        if ($object instanceof IComponentContext) {
            $object->setContextComponent(
                $this->hasContextComponent() ? $this->getContextComponent() : null
            );
        }
    }
}
 