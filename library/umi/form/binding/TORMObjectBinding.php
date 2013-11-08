<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\form\binding;

use umi\orm\object\IObject;

/**
 * Трейт биндинга данных в объект, как в массив.
 * Объект должен поддерживать \ArrayAccess, чтобы
 * этот трейт работал.
 * todo: добавить поддержку event'ов
 */
trait TORMObjectBinding
{

    /**
     * Устанавливает данные в объект.
     * @param array $data
     * @throws \Exception
     * @return $this
     */
    public function setData(array $data)
    {
        if (!$this instanceof IObject) {
            throw new \Exception();
        }

        foreach ($data as $key => $value) {
            if ($this->getPropertyExists($key)) {
                $this->setValue($key, $value);
            }
        }

        return $this;
    }

    /**
     * Возвращает данные из объекта.
     * @throws \Exception
     * @return array
     */
    public function getData()
    {
        if (!$this instanceof IObject) {
            throw new \Exception();
        }

        $properties = $this->getAllProperties();

        $data = [];
        foreach ($properties as $property) {
            $data[$property->getName()] = $property->getValue();
        }

        return $data;
    }
}