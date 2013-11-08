<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\metadata;

use umi\orm\exception\RequiredDependencyException;

/**
 * Трейт для внедрения менеджера для работы с метаданными.
 */
trait TMetadataManagerAware
{
    /**
     * @var IMetadataManager $_metadataManager менеджер для работы с метаданными
     */
    private $_metadataManager;

    /**
     * Устанавливает менеджер для работы с метаданными
     * @param IMetadataManager $metadataManager
     */
    public function setMetadataManager(IMetadataManager $metadataManager)
    {
        $this->_metadataManager = $metadataManager;
    }

    /**
     * Возвращает менеджер для работы с метаданными
     * @throws RequiredDependencyException если менеджер для работы с метаданными не установлен
     * @return IMetadataManager
     */
    protected function getMetadataManager()
    {
        if (!$this->_metadataManager) {
            throw new RequiredDependencyException(sprintf(
                'Metadata manager is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_metadataManager;
    }
}
