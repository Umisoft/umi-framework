<?php
namespace umi\authentication\storage;

use umi\authentication\storage\SessionStorage;
use umi\orm\manager\IObjectManagerAware;
use umi\orm\manager\TObjectManagerAware;
use umi\orm\object\IObject;
use umi\session\ISession;

/**
 * Класс Session Storage
 */
class ORMSessionStorage extends SessionStorage implements IObjectManagerAware
{
    use TObjectManagerAware;

    /**
     * Конструктор.
     */
    public function __construct(array $config = [], ISession $session)
    {
        parent::__construct($config, $session);

        if ($this->hasIdentity()) {
            $this->wakeUpIdentity($this->getIdentity());
        }
    }

    /**
     * Восстанавливает пользователя в ORM
     * @param IObject $identity
     */
    protected function wakeUpIdentity(IObject $identity)
    {
        $this->getObjectManager()
            ->wakeUpObject($identity);
    }
}