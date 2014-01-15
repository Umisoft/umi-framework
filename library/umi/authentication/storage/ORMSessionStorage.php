<?php
namespace umi\authentication\storage;

use umi\authentication\exception\RuntimeException;
use umi\orm\manager\IObjectManagerAware;
use umi\orm\manager\TObjectManagerAware;
use umi\orm\object\IObject;

/**
 * Хранилище ORM-данных сессии
 */
class ORMSessionStorage extends SessionStorage implements IObjectManagerAware
{
    use TObjectManagerAware;

    private $needToWakeUp = true;

    /**
     * {@inheritdoc}
     */
    public function getIdentity()
    {

        $identity = parent::getIdentity();

        if (!$identity instanceof IObject) {
            throw new RuntimeException(
                'Identity should be instance of IObject.'
            );
        }

        if ($this->needToWakeUp) {
            $this->wakeUpIdentity($identity);
            $this->needToWakeUp = false;
        }

        return $identity;
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