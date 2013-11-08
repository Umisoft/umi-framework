<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\metadata\field\special;

use umi\orm\exception\InvalidArgumentException;
use umi\orm\metadata\field\BaseField;
use umi\orm\metadata\field\IScalarField;
use umi\orm\metadata\field\TScalarField;
use umi\orm\object\IObject;

/**
 * Глобальный идентификатор объекта (guid).
 */
class GuidField extends BaseField implements IScalarField
{

    use TScalarField;

    /**
     * {@inheritdoc}
     */
    public function getDataType()
    {
        return 'string';
    }

    /**
     * {@inheritdoc}
     */
    public function validateInputPropertyValue($propertyValue)
    {
        if (!strlen($propertyValue)) {
            throw new InvalidArgumentException($this->translate(
                'GUID cannot be empty.'
            ));
        }

        if (!$this->checkGUIDFormat($propertyValue)) {
            throw new InvalidArgumentException($this->translate(
                'Wrong format for GUID "{guid}".',
                ['guid' => $propertyValue]
            ));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDbValue(IObject $object, $propertyValue)
    {
        return strtolower($propertyValue);
    }

    /**
     * Создает GUID для объекта.
     * @return string
     */
    public function generateGUID()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,
            // 48 bits for "node"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * Проверяет, имеет ли GUID правильный формат
     * @param string $guid
     * @return bool
     */
    public function checkGUIDFormat($guid)
    {
        return strlen($guid) && preg_match('#^\S{8}-\S{4}-\S{4}-\S{4}-\S{12}$#', $guid);
    }

}
