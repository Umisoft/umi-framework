<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\mock\collections;

use umi\orm\object\IObject;
use umi\orm\object\Object;

/**
 * Тестовый Object
 * @property int $id
 * @property string $login логин пользователя
 * @property string $password пароль пользователя
 * @property bool isActive активность
 */
class User extends Object
{
    private $salt = '$2a$10$jowekSSFflszklswiof932as8SDoiwepo2uiwierwim2w';

    /**
     * Возвращает пароль пользователя
     * @return string
     */
    public function getPassword()
    {
        return $this->getProperty('password')
            ->getValue();
    }

    /**
     * Устанавливает новый пароль пользователя. Солит, маринует и хэширует.
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $salt = substr($this->salt, 0, 29);
        $cryptPassword = crypt($password, $salt);
        $this->getProperty('password')
            ->setValue($cryptPassword);

        return $this;
    }

    /**
     * Возвращает логин пользователя
     * @return string
     */
    public function getLogin()
    {
        return $this->getProperty('login')
            ->getValue();
    }

    /**
     * Устанавливает новый логин пользователя.
     * @param string $login
     * @return $this
     */
    public function setLogin($login)
    {
        $this->getProperty('login')
            ->setValue($login);

        return $this;
    }

    /**
     * Проверяет валидность логина
     * @return bool
     */
    public function validateLogin()
    {
        $result = true;
        $login = $this->getLogin();

        if (strlen($login) < 3) {
            $result = false;
            $this->addValidationError('login', ['Login is shorter than 3 symbols']);
        }

        $users = $this->getCollection()
            ->select()
            ->fields([IObject::FIELD_IDENTIFY])
            ->where('login')
            ->equals($login)
            ->getResult();

        if (count($users->fetchAll())) {
            $result = false;
            $this->addValidationError('login', ['Login is not unique']);
        }

        return $result;
    }

    /**
     * Метод используется для тестирования, когда валидатор ничего не возвращает
     */
    public function validateRating()
    {

    }
}
