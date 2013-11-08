<?php
/**
 * Словарь для локализации системных сообщений моделей данных
 */
return array(

    //ObjectManager
    'Cannot create object. Class "{class}" doesn\'t exist.'                                                                     => 'Невозможно создать объект. Класс "{class}" не существует.',
    'Cannot load object from data row. Field alias "{alias}" is not correct.'                                                   => 'Невозможно загрузить объект из записи базы данных. Некорректный алиас "{path}" для поля',
    'Cannot load object from data row. Information about object type is not found.'                                             => 'Невозможно загрузить объект из записи базы данных. Информация о типе объекта не найдена',
    'Cannot load object from data row. Object type path "{path}" is not correct.'                                               => 'Невозможно загрузить объект из записи базы данных. Некорректный путь "{path}" к типу объекта',
    'Cannot get object with id "{id}" from collection "{collection}".'                                                          => 'Не удалось получить объект с id "{id}" из коллекции "{collection}".',
    'Cannot persist objects: {message}'                                                                                         => 'Невозможно сохранить объекты: {message}',
    'Cannot get Selector for ObjectSet.'                                                                                        => 'Не удалось получить Selector for ObjectSet.',
    'Cannot lazy load object. Object id required.'                                                                              => 'Невозможно дозагрузить объект. Требуется id объекта.',
    'Cannot load object. Primary key value is not found.'                                                                       => 'Невозможно загрузить объект. Не найдена информация об идентификаторе.',
    'Hierarchy class "{class}" should implement IHierarchy.'                                                                    => 'Класс иерархической коллекции "{class}" должен реализовывать интерфейс IHierarchy.',
    'Class of hierarchic object "{class}" should implement IHierarchicObject.'                                                  => 'Класс иерархического объекта "{class}" должен реализовывать интерфейс IHierarchicObject',
    //TObjectPersister
    'Cannot persist object. Cannot get last inserted id for object.'                                                            => 'Не удалось сохранить объект. Не возможно получить выставленный объекту id',
    'Cannot set hierarchy fields for object with id "{id}" and type "{type}". Database row is not modified.'                    => 'Не удалось выставить значение иерархических свойств для объекта с id "{id}" и типом "{type}". Соответвующая запись в базе данных не была изменена.',
    'Cannot modify object with id "{id}" and type "{type}". Database row is not modified.'                                      => 'Не удалось обновить объект с id "{id}" и типом "{type}". Соответвующая запись в базе данных не была изменена.',
    'Cannot modify object with id "{id}" and type "{type}". Version of the object is out of date.'                              => 'Не удалось обновить объект с id "{id}" и типом "{type}". Версия объекта устарела.',
    'Cannot delete object with id "{id}" and type "{type}". Database row is not modified.'                                      => 'Не удалось удалить объект с id "{id}" и типом "{type}". Соответвующая запись в базе данных не была изменена.',
    //CollectionDataSource
    'Source name is not injected for metadata.'                                                                                 => 'Не установлено имя источника для метаданных "{metadata}".',
    //Field
    'Cannot set relation type. Type "{type}" is unknown.'                                                                       => 'Невозможно выставить тип связи. Тип "{type}" не известен.',
    'Cannot get column name for field "{field}" in locale "{locale}".'                                                          => 'Не удалось получить имя колонки у поля "{field}" для локали "{locale}".',
    'Cannot get default value for field "{field}" in locale "{locale}".'                                                        => 'Не удалось получить значение по умолчанию поля "{field}" для локали "{locale}".',
    //Metadata
    'Cannot return table scheme. Metadata is readonly.'                                                                         => 'Невозможно получить схему источника данных коллекции. Метаданные доступны только для чтения.',
    'Object type "{name}" does not exist in "{collection}".'                                                                    => 'Объектный тип с именем "{name}" не существует в метаданных "{collection}".',
    'Object type "{name}" already exists in "{collection}".'                                                                    => 'Объектный тип с именем "{name}" уже существует в метаданных "{collection}".',
    'Cannot create type "{name}". Base type does not exist in "{collection}".'                                                  => 'Невозможно создать объектный тип с именем "{name}". В метаданных "{collection}" отсутствует базовый тип.',
    'Cannot create type "{name}". Parent type does not exist in "{collection}".'                                                => 'Невозможно создать объектный тип с именем "{name}". В метаданных "{collection}" отсутствует родительский тип объекта.',
    'It is not allowed to delete base type.'                                                                                    => 'Нельзя удалить базовый класс.',
    'Field group "{name}" does not exist in "{collection}".'                                                                    => 'Группа полей с именем "{name}" не существует в метаданных "{collection}".',
    'Field group "{name}" already exists in "{collection}".'                                                                    => 'Группа полей с именем "{name}" уже существует в метаданных "{collection}".',
    'Cannot rename fields group "{name}" to "{newName}". Field group "{newName}" already exists in "{collection}".'             => 'Невозможно переименовать группу полей с именем "{name}" в "{newName}". Группа с именем "{newName}" уже существует в метаданных "{collection}".',
    'Field "{name}" does not exist in "{collection}".'                                                                          => 'Поле с именем "{name}" не существует в метаданных "{collection}".',
    'Field "{name}" already exists in "{collection}".'                                                                          => 'Поле с именем "{name}" уже существует в метаданных "{collection}".',
    'Cannot rename field "{name}" to "{newName}". Field "{newName}" already exists in "{collection}".'                          => 'Невозможно переименовать поле с именем "{name}" в "{newName}". Поле "{newName}" уже существует в метаданных "{collection}".',
    'Cannot apply modifications. Metadata is readonly.'                                                                         => 'Невозможно сохранить изменения. Метаданные доступны только для чтения.',
    'It is not allowed to commit metadata without base type.'                                                                   => 'Нельзя сохранить метаданные без базового типа.',
    'Field "{name}" cannot be detached from type "{type}" in "{collection}", since it is inherited from the parent type.'       => 'Невозможно открепить поле с именем "{name}" от типа с именем "{type}" в метаданных "{collection}", так оно унаследовано от родительскго типа.',
    'Field group "{name}" cannot be detached from type "{type}" in "{collection}", since it is inherited from the parent type.' => 'Невозможно открепить группу полей с именем "{name}" от типа с именем "{type}" в метаданных "{collection}", так как она унаследована от родительскго типа.',
    //MetadataXMLSource
    'Cannot find field with relation bridge "{bridge}" and related field "{relatedField}".'                                     => 'Не найдено поле, использующее bridge-коллекцию "{bridge}" и связанное поле "{relatedField}".',
    'Cannot find field with relation target "{target}" and target field "{targetField}".'                                       => 'Не найдено поле, использующее target-коллекцию "{target}" и связанное поле "{targetField}".',
    'Cannot load metadata for type "{name}".'                                                                                   => 'Не удалось получить метаданные объектного типа с именем "{name}".',
    'Cannot load metadata for fields group "{name}".'                                                                           => 'Не удалось получить метаданные группы полей с именем "{name}".',
    'Cannot load metadata for field "{name}".'                                                                                  => 'Не удалось получить метаданные поля с именем "{name}".',
    'Cannot load metadata configuration file "{file}": "{message}".'                                                            => 'Не могу загрузить конфигурационный файл с метаданными "{file}": "{message}".',
    //ObjectType
    'Object type "{name}" does not contain primary key field.'                                                                  => 'Объектный тип с именем "{name}" не содержит поле для хранения идентификатора.',
    'Object type "{name}" does not contain field for object type.'                                                              => 'Объектный тип с именем "{name}" не содержит поле для хранения информации о типе объекта.',
    'Object type "{name}" does not contain field for parent.'                                                                   => 'Объектный тип с именем "{name}" не содержит поле для хранения информации о родителе объекта.',
    'Object type "{name}" does not contain field for materialized path.'                                                        => 'Объектный тип с именем "{name}" не содержит поле для хранения информации о материализованном пути объекта.',
    'Object type "{name}" does not contain field for hierarchy order.'                                                          => 'Объектный тип с именем "{name}" не содержит поле для хранения информации о порядке объекта в иерархии.',
    'Object type "{name}" does not contain field for hierarchy level.'                                                          => 'Объектный тип с именем "{name}" не содержит поле для хранения информации о уровне вложенности объекта в иерархии.',
    'Object type "{name}" does not contain field for version.'                                                                  => 'Объектный тип с именем "{name}" не содержит поле для хранения информации о версии объекта.',
    //BelongsToRelation
    'Cannot set value for property "{name}". Value must be instance of IObject.'                                                => 'Не удалось выставить значения для свойства "{name}". Значение должно быть экземпляром IObject.',
    'Cannot set value for property "{name}". IObject from wrong collection is given.'                                           => 'Не удалось выставить значения для свойства "{name}". Значение принадлежит неподходящей коллекции.',
    //HasManyRelation
    'Cannot prepare value for property "{name}". ObjectSet for HasManyRelation required.'                                       => 'Не удалось получить значение для свойства "{name}". Для полей с типом связи HasManyRelation требуется ObjectSet.',
    'Cannot set value for property "{name}". Value should be set on relation owner side.'                                       => 'Не удалось выставить значения для свойства "{name}". Згачение должно быть выставлено со стороны владельца связи.',
    //ManyToManyRelation
    'Cannot prepare value for property "{name}". ManyToManyObjectSet for ManyToManyRelation required.'                          => 'Не удалось получить значение для свойства "{name}". Для полей с типом связи ManyToManyRelation требуется ManyToManyObjectSet.',
    //ScalarProperty
    'Cannot set value for property "{name}". Value must be scalar.'                                                             => 'Не удалось выставить значения для свойства "{name}". Значение должно быть скалярным.',
    //HierarchicObject
    'Cannot set parent for object with id "{id}". Object already has a parent.'                                                 => 'Не удалось выставить родителя для объекта с id "{id}". У объекта уже есть родитель.',
    //ManyToManyObjectSet
    'Cannot attach object to ManyToManyObjectSet. This object is already attached.'                                             => 'Не удалось добавить объект в ManyToManyObjectSet. Этот объект уже был добавлен.',
    'Cannot attach object to ManyToManyObjectSet. Linked object required.'                                                      => 'Не удалось добавить объект в ManyToManyObjectSet. Не удалось получить объект связи.',
    'Cannot get linked object. IObject from wrong collection is given.'                                                         => 'Не удалось получить объект связи. Передан объект из неподходящей коллекции.',
    //Object
    'Cannot set value for property "{name}". Property is read only.'                                                            => 'Невозможно выставить значение для свойства "{name}". Свойство доступно только для чтения.',
    //ObjectSet
    'Cannot fetch ObjectSet. Query result expected.'                                                                            => 'Невозможно итерировать ObjectSet. Требуется результат выборки из базы данных.',
    //Selector
    'Cannot select objects. Object type "{name}" does not exist.'                                                               => 'Невозможно произвести выборку. Не существует объектного типа с именем "{name}".',
    'Cannot select objects. Field "{name}" does not exist.'                                                                     => 'Невозможно произвести выборку. Не существует поля с именем "{name}".',
    'Cannot resolve field path "{path}". Field "{name}" does not exist in "{metadata}".'                                        => 'Не удалось получить поля в цепочке "{path}". Поле с именем "{name}" не существует в метаданных "{metadata}".',
    'Cannot resolve field path "{path}". Field "{name}" is not relation.'                                                       => 'Не удалось получить поля в цепочке "{path}". Поле с именем "{name}" имеет тип отличный от relation.',
    'The selection is not possible. Conditions do not match metadata types.'                                                    => 'Невозможно произвести выборку. Условия выборки не соответвуют структуре типов в метаданных.',
    //ValidatorsCollection
    'Cannot get validator "{name}". Class of validator is not defined.'                                                         => 'Не удалось получить валидатор с именем "{name}". Неопределен класс валидатора.',
    'Cannot get validator "{name}". Class "{class}" of validator does not exist.'                                               => 'Не удалось получить валидатор с именем "{name}". Класс "{class}" не существует.',
    'Cannot add validator "{name}". Validator already exists.'                                                                  => 'Невозможно создать валидатор с именем "{name}". Валидатор уже существует.',
    //Model
    'Object collection "{collection}" is not registered in "{model}".'                                                          => 'Коллекция объектов "{collection}" не зарегистрирована в "{model}".',
    'Cannot create collection "{name}". Class "{class}" does not exist.'                                                        => 'Невозможно создать коллекцию "{name}". Класс "{class}" не существует.',
    //ModelCollection
    'Cannot get validators collection. Config file path required.'                                                              => 'Не удалось получить коллекцию валидаторов. Требуется конфигурационный файл.',
    'Model "{name}" does not exist.'                                                                                            => 'Модель с именем "{name}" не существует в коллекции моделей.',
    'Cannot get model "{name}". Class "{class}" does not exist.'                                                                => 'Не возможно получить модель с именем "{name}". Класс "{class}" не существует.',
    'Cannot get collection. Path "{path}" is invalid.'                                                                          => 'Невозможно получить коллекцию. Путь "{path}" не корректен.',
    'Cannot add model "{name}". Model already exists.'                                                                          => 'Невозможно добавить модель с именем "{name}". Модель уже существует в коллекции моделей.',
    'Cannot delete model "{name}". Model does not exist.'                                                                       => 'Невозможно удалить модель с именем "{name}". Модель не существует в коллекции моделей.',
    'Cannot add collection "{collection}" to model "{model}". Model does not exist.'                                            => 'Невозможно добавить коллекцию объектов "{collection}" в модель "{model}". Модель не существует в коллекции моделей.',
    'Cannot add collection "{collection}" to model "{model}". Collection already exists.'                                       => 'Невозможно добавить коллекцию объектов "{collection}" в модель "{model}". Коллекция уже существует.',
    'Cannot delete collection "{collection}" from model "{model}". Model does not exist.'                                       => 'Невозможно удалить коллекцию объектов "{collection}" из модели "{model}". Модель не сущетсвует.',
    'Cannot delete collection "{collection}" from model "{model}". Collection does not exist.'                                  => 'Невозможно удалить коллекцию объектов "{collection}" из модели "{model}". Коллекция не сущетсвует.',
    'Cannot remove database migrations: "{message}".'                                                                           => 'Не удалось удалить файл с миграциями базы данных: "{message}".',
    'Cannot save database migrations: "{message}".'                                                                             => 'Не удалось удалить файл с миграциями базы данных: "{message}".',
    'Cannot save database migrations. Zero file size.'                                                                          => 'Не удалось удалить файл с миграциями базы данных. Файл пустой.',
    'Cannot load models configuration file "{name}": "{message}".'                                                              => 'Не удалось загрузить конфигурационный файл моделей "{name}": "{message}".'
);