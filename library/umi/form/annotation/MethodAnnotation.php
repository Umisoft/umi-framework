<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\form\annotation;

use umi\form\IForm;
use umi\form\IFormEntity;

/**
 * Аннотация для установки названия элемента.
 */
class MethodAnnotation extends BaseAnnotation
{

    /**
     * {@inheritdoc}
     */
    public function transform(IFormEntity $entity)
    {
        if (!$entity instanceof IForm) {
            throw new \Exception();
        }

        $entity->getAttributes()['method'] = $this->value;
    }
}