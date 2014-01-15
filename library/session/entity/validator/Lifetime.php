<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\session\entity\validator;

use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\session\entity\ns\ISessionNamespace;
use umi\session\exception\RuntimeException;

/**
 * Валидатор сессии по времени.
 */
class Lifetime implements ISessionValidator, ILocalizable
{

    use TLocalizable;

    /**
     * @var int $lifetime время жизни валидатора
     */
    protected $lifetime;

    /**
     * Ключ в метаданных для хранения параметра времени последнего доступа.
     */
    const META_KEY = 'LastAccess';

    /**
     * Конструктор.
     */
    public function __construct($lifetime)
    {
        $this->lifetime = $lifetime;

        if ($this->lifetime < 0) {
            throw new RuntimeException($this->translate(
                'Option "lifetime" should be a positive number.'
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ISessionNamespace $session)
    {
        if (is_null($session->getMetadata(self::META_KEY))) {
            $session->setMetadata(self::META_KEY, time());
        }

        if (time() - $session->getMetadata(self::META_KEY) > $this->lifetime) {
            return false;
        }

        $session->setMetadata(self::META_KEY, time());

        return true;
    }
}