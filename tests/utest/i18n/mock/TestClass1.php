<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\i18n\mock;

use umi\i18n\ILocalizable;
use umi\i18n\translator\ITranslator;

/**
 * Class TestClass1
 */
class TestClass1 implements ILocalizable
{

    /**
     * {@inheritdoc}
     */
    public function setTranslator(ITranslator $translator)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getI18nDictionaries()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function translate($message, array $placeholders = array(), $localeId = null)
    {
        $i = 1 + 2;

        return $i;
    }

    public function testMethod1()
    {
        throw new \RuntimeException($this->translate(
            '1st test {label}".',
            ['label' => 'label']
        ));
    }

    public function testMethod2()
    {
        $this->translate('2nd test label.');
    }
}
