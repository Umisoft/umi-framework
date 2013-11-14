<?php
namespace umi\authentication\adapter;

use umi\authentication\adapter\IAuthAdapter;
use umi\authentication\exception\InvalidArgumentException;
use umi\authentication\result\IAuthenticationResultAware;
use umi\authentication\result\IAuthResult;
use umi\authentication\result\TAuthenticationResultAware;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\orm\collection\ICollectionManagerAware;
use umi\orm\collection\TCollectionManagerAware;
use umi\orm\object\IObject;
use umi\orm\selector\condition\IFieldConditionGroup;
/**
 * Адаптер для аутентификации с помощью ORM коллекции пользователей
 */
class ORMAdapter implements IAuthAdapter, ICollectionManagerAware, IAuthenticationResultAware, ILocalizable
{
    use TLocalizable;
    use TAuthenticationResultAware;
    use TCollectionManagerAware;

    /** Имя коллекции */
    const OPTION_COLLECTION = 'collection';
    /** Поля содержащие логин */
    const OPTION_LOGIN_FIELDS = 'loginFields';
    /** Поле содержащее пароль */
    const OPTION_PASSWORD_FIELD = 'passwordField';

    /**
     * @var string $collection имя коллекции пользователей
     */
    protected $collectionName;
    /**
     * @var array $loginFields поля коллекции, которые могут быть использованы для идентификации пользователя
     */
    protected $usernameFields = [];
    /**
     * @var string $passwordField поле коллекции, в котором хранится хэш пароля
     */
    protected $passwordField;

    /**
     * Конструктор
     * @param array $options
     * @throws InvalidArgumentException если обязательные опции не переданы
     */
    public function __construct(array $options = [])
    {
        if (!isset($options[self::OPTION_COLLECTION]) ||
            !isset($options[self::OPTION_LOGIN_FIELDS]) ||
            !isset($options[self::OPTION_PASSWORD_FIELD])) {

            throw new InvalidArgumentException($this->translate(
                'Options "collection", "loginFields", "passwordField" is required.'
            ));
        }

        $this->collectionName = $options[self::OPTION_COLLECTION];
        $this->usernameFields = $options[self::OPTION_LOGIN_FIELDS];
        $this->passwordField = $options[self::OPTION_PASSWORD_FIELD];

    }

    /**
     * {@inheritdoc}
     */
    public function authenticate($username, $password)
    {
        $usersSelector = $this->getCollectionManager()
            ->getCollection($this->collectionName)
            ->select();

        $usersSelector->begin(IFieldConditionGroup::MODE_OR);
        foreach ($this->usernameFields as $fieldName) {
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