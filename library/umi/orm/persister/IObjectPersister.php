<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\persister;

use umi\orm\exception\NotAllowedOperationException;
use umi\orm\exception\RuntimeException;
use umi\orm\metadata\field\relation\BelongsToRelationField;
use umi\orm\object\IObject;

/**
 * Синхронизатор объектов бизнес-транзакций с базой данных (Unit Of Work).
 */
interface IObjectPersister
{

    /**
     * Проверяет, являются ли все объекты в памяти синхронизированными с БД
     * @return bool
     */
    public function getIsPersisted();

    /**
     * Запускает произвольную бизнес-транзакцию.
     * @param callable $transaction callable-функция, выполняемая внутри транзакции.
     * Если при транзакция выкидывает исключение, происходит автоматический rollback запросов
     * @param array $affectedDrivers драйверы, которые задействованы в транзакции
     * @throws NotAllowedOperationException если есть не завершенная бизнес-транзакция.
     * @throws RuntimeException если транзакция завершилась не успешно
     */
    public function executeTransaction(callable $transaction, array $affectedDrivers = []);

    /**
     * Записывает изменения всех объектов в БД (бизнес транзакция).
     * Валидация объектов при этом не выполняется. Получить список невалидных объектов
     * можно вызвав метод getInvalidObjects().
     * Если при сохранении какого-либо объекта возникли ошибки - все изменения
     * автоматически откатываются
     * @throws RuntimeException если транзакция не успешна
     * @return self
     */
    public function commit();

    /**
     * Валидирует все измененные и новые объекты, возвращает список невалидных объектов.
     * Запускается автоматически на момент коммита.
     * @return IObject[] массив невалидных объектов, либо пустой массив, если все измененные объекты валидны
     */
    public function getInvalidObjects();

    /**
     * Валидирует объект
     * @param IObject $object
     * @return bool результат валидации
     */
    public function validateObject(IObject $object);

    /**
     * Помечает объект как новый
     * @param IObject $object
     * @return self
     */
    public function markAsNew(IObject $object);

    /**
     * Помечает объект как удаленный
     * @param IObject $object
     * @return self
     */
    public function markAsDeleted(IObject $object);

    /**
     * Помечает объект как измененный
     * @param IObject $object
     * @return self
     */
    public function markAsModified(IObject $object);

    /**
     * Сохраняет новую BelongsTo-связь для объекта.
     * @internal
     * @param BelongsToRelationField $belongsToRelation поле связи
     * @param IObject $object объект, для которого выставляется связь
     * @param IObject $relatedObject объект, который устанавливается в качестве значения связи
     */
    public function storeNewBelongsToRelation(
        BelongsToRelationField $belongsToRelation,
        IObject $object,
        IObject $relatedObject
    );

    /**
     * Очищает списки добавленных, модифицированных, перемещенныз и удаленных объектов.
     * Все изменения, которые были не применены будут утеряны.
     * @internal
     * @return self
     */
    public function clearObjectsState();

    /**
     * Выгружает объект из всех хранилищ.
     * @internal
     * @param IObject $object
     * @return self
     */
    public function clearObjectState(IObject $object);
}
