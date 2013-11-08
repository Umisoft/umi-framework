<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\driver\sqlite;

use umi\dbal\driver\IndexScheme;

/**
 * Индекс таблицы SQLite.
 */
class SqliteIndex extends IndexScheme
{

    /**
     * {@inheritdoc}
     */
    public function addColumn($name, $length = null)
    {

        if (!isset($this->columns[$name])) {
            $this->columns[$name] = array(
                'name'   => $name,
                'length' => $length
            );
            $this->setIsModified(true);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        return $this;
    }
}
