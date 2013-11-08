<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\form\annotation;

use umi\form\element\IElement;
use umi\form\IFormEntity;
use umi\validation\IValidationAware;
use umi\validation\IValidatorCollection;
use umi\validation\TValidationAware;

/**
 * Аннотация для установки валидаторов в элемент формы.
 */
class ValidatorAnnotation extends BaseAnnotation implements IValidationAware
{

    use TValidationAware;

    /**
     * {@inheritdoc}
     */
    public function transform(IFormEntity $entity)
    {
        if (!$entity instanceof IElement) {
            throw new \Exception();
        }

        if (!$this->value instanceof IValidatorCollection) {
            $this->value = $this->createValidatorCollection($this->value);
        }

        $entity->getValidators()
            ->appendValidator($this->value);
    }
}