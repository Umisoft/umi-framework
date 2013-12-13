<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\form\element;

use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\session\ISessionAware;
use umi\session\TSessionAware;

/**
 * Элемент формы - Cross-site request forgery токен.
 * @example <input type="hidden" value="ca969a1bc97732d97b1e88ce8396c216" />
 */
class CSRF extends Hidden implements ILocalizable, ISessionAware
{
    use TLocalizable;
    use TSessionAware;

    /**
     * Тип элемента.
     */
    const TYPE_NAME = 'csrf';
    /**
     * Пространство имен сессии для хранения данных о CSRF.
     */
    const SESSION_NAMESPACE = 'csrf_protection';

    /**
     * @var string $token CSRF токен
     */
    protected $token;

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        return $this->token == parent::getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value, $isRaw = false)
    {
        if (!$this->token) {
            $this->initToken();
        }

        parent::setValue($value, $isRaw);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        if (!$this->token) {
            $this->initToken();
        }

        return $this->token;
    }

    /**
     * Восстанавливает значение токена из сессии,
     * либо генерирует новый токен.
     * todo: refactor that
     */
    protected function initToken()
    {
        if (!$this->hasSessionNamespace(self::SESSION_NAMESPACE)) {
            $this->registerSessionNamespace(self::SESSION_NAMESPACE);
        }

        $session = $this->getSessionNamespace(self::SESSION_NAMESPACE);

        $this->token = $session->get('token');

        if (!$this->token) {
            $this->token = sha1('token:' . time() . rand());
            $session->set('token', $this->token);
        }
    }
}