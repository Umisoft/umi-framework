<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\form\unit\element;

use umi\form\element\Submit;

/**
 * Тесты элемента формы - Отправить
 */
class SubmitElementTest extends ButtonElementTest
{
    /**
     * {@inheritdoc}
     */
    public function getElement($name, array $attributes = [], array $options = [])
    {
        $e = new Submit($name, $attributes, $options);

        $this->resolveOptionalDependencies($e);

        return $e;
    }
}