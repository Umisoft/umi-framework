<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\form\annotation;

use umi\filter\IFilterAware;
use umi\filter\IFilterCollection;
use umi\filter\TFilterAware;
use umi\form\element\IElement;
use umi\form\IFormEntity;

/**
 * Аннотация для установки фильтров в элемент формы.
 */
class FilterAnnotation extends BaseAnnotation implements IFilterAware
{

    use TFilterAware;

    /**
     * {@inheritdoc}
     */
    public function transform(IFormEntity $entity)
    {
        if (!$entity instanceof IElement) {
            throw new \Exception();
        }

        if (!$this->value instanceof IFilterCollection) {
            $this->value = $this->createFilterCollection($this->value);
        }

        $entity->getFilters()
            ->appendFilter($this->value);
    }
}