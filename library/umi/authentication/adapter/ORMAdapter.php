<?php
namespace umicms\library\authentication\adapter;

use umi\authentication\adapter\IAuthAdapter;
use umi\authentication\result\IAuthenticationResultAware;
use umi\authentication\result\IAuthResult;
use umi\authentication\result\TAuthenticationResultAware;
use umi\orm\collection\ICollectionManagerAware;
use umi\orm\collection\TCollectionManagerAware;
use umi\orm\object\IObject;
use umi\orm\selector\condition\IFieldConditionGroup;
/**
 * Адаптер для аутентификации с помощью ORM коллекции пользователей
 */
class ORMAdapter implements IAuthAdapter, ICollectionManagerAware, IAuthenticationResultAware
{
    use TAuthenticationResultAware;
    use TCollectionManagerAware;

    /**
     * @var string $collection имя коллекции пользователей
     */
    public $collectionName;
    /**
     * @var array $loginFields поля коллекции, которые могут быть использованы для идентификации пользователя
     */
    public $loginFields = [];
    /**
     * @var string $passwordField поле коллекции, в котором хранится хэш пароля
     */
    public $passwordField;

    /**
     * {@inheritdoc}
     */
    public function authenticate($username, $password)
    {
        $usersSelector = $this->getCollectionManager()
            ->getCollection($this->collectionName)
            ->select();

        $usersSelector->begin(IFieldConditionGroup::MODE_OR);
        foreach ($this->loginFields as $fieldName) {
            $usersSelector->where($fieldName)
                ->equals($username);
        }
        $usersSelector->end();
        $usersSelector->limit(1);
        $usersSelector->withLocalization();

        $user = $usersSelector->result()
            ->fetch();

        if (!$user instanceof IObject) {
            return $this->createAuthResult(IAuthResult::WRONG_USERNAME);
        } elseif (!$this->checkPassword($user, $password)) {
            return $this->createAuthResult(IAuthResult::WRONG_PASSWORD);
        } else {
            return $this->createAuthResult(IAuthResult::SUCCESSFUL, $user);
        }
    }

    /**
     * Проверяет правильность пароля для указанного пользователя
     * @param IObject $user пользователь
     * @param string $password пароль
     * @return bool true, если пароль верный
     */
    public function checkPassword(IObject $user, $password)
    {
        return $user->getValue($this->passwordField) === $password;
    }
}