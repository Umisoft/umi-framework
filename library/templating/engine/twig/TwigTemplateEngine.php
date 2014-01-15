<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\engine\twig;

use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\templating\engine\BaseTemplateEngine;
use umi\templating\extension\adapter\IExtensionAdapter;

/**
 * Twig шаблонизатор.
 */
class TwigTemplateEngine extends BaseTemplateEngine implements ILocalizable
{
    use TLocalizable;

    /**
     * @var \Twig_Environment $twigEnv окружение шаблонизатора Twig
     */
    protected $twigEnvironment;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $options)
    {
        parent::__construct($options);

        $baseDirectory = isset($options[self::OPTION_DIRECTORY]) ? $options[self::OPTION_DIRECTORY] : '';
        $twigLoader = new \Twig_Loader_Filesystem($baseDirectory);

        $this->twigEnvironment = new \Twig_Environment($twigLoader);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAdapter(IExtensionAdapter $adapter)
    {
        parent::setExtensionAdapter($adapter);

        foreach ($adapter->getRegisteredHelperCollection() as $collectionName) {
            $this->twigEnvironment->addExtension(
                new TwigHelperExtension(
                    $collectionName,
                    $adapter->getHelperCollection($collectionName)
                )
            );
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function render($template, array $arguments = [])
    {
        return $this->twigEnvironment->render(
            $this->getTemplateFilename($template),
            $arguments
        );
    }
}