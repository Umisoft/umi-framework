<?php
namespace umi\authentication\storage;

use umi\orm\manager\IObjectManagerAware;
use umi\orm\manager\TObjectManagerAware;
use umi\orm\object\IObject;
use umi\session\ISession;

/**
 * Хранилище ORM-данных сессии
 */
class ORMSessionStorage extends SessionStorage implements IObjectManagerAware
{
    use TObjectManagerAware;

    /**
     * Конструктор.
     * @param ISession $session
     * @param array $config конфигурация
     */
    public function __construct(ISession $session, array $config = [])
    {
        parent::__construct($session, $config);

        if ($this->hasIdentity()) {
            $this->wakeUpIdentity($this->getIdentity());
        }
    }

    /**
     * Восстанавливает пользователя в ORM.
     * @param IObject $identity
     */
    protected function wakeUpIdentity(IObject $identity)
    {
        $this->getObjectManager()
            ->wakeUpObject($identity);
    }
}