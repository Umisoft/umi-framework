<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\templating\mock\toolbox;

use umi\templating\engine\ITemplateEngineAware;
use umi\templating\engine\TTemplateEngineAware;
use utest\IMockAware;

/**
 * Class MockTemplatingAware
 */
class MockTemplatingAware implements ITemplateEngineAware, IMockAware
{
    use TTemplateEngineAware;

    /**
     * {@inheritdoc}
     */
    public function getService()
    {
        return $this->_templatingFactory;
    }
}
 