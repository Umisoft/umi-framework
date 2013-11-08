<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\extension\helper\type;

use Traversable;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * Помошник вида для перевода сообщений.
 */
class TranslateHelper implements ILocalizable
{

    use TLocalizable;

    /**
     * @var array|Traversable $dictionaries список словарей
     */
    public $dictionaries = [
        'app'
    ];

    /**
     * {@inheritdoc}
     */
    public function getI18nDictionaries()
    {
        if ($this->dictionaries instanceof Traversable) {
            $this->dictionaries = iterator_to_array($this->dictionaries, true);
        }

        return $this->dictionaries;
    }

    /**
     * Переводит сообщение
     * @param string $message сообщение
     * @param array $placeholders список плейсхолдеров
     * @return string
     */
    public function __invoke($message, array $placeholders = [])
    {
        return $this->translate($message, $placeholders);
    }
}