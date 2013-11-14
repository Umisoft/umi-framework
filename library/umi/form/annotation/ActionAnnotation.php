<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\form\annotation;

use umi\form\exception\InvalidArgumentException;
use umi\form\IForm;
use umi\form\IFormEntity;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * Аннотация для установки названия элемента.
 */
class ActionAnnotation extends BaseAnnotation implements ILocalizable
{
    use TLocalizable;

    /**
     * {@inheritdoc}
     */
    public function transform(IFormEntity $entity)
    {
        if (!$entity instanceof IForm) {
            throw new InvalidArgumentException(
                $this->translate('Validator can be added only to elements.')
            );
        }

        $entity->getAttributes()['action'] = $this->value;
    }
}