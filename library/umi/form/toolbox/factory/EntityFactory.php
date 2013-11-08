<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\form\toolbox\factory;

use umi\form\annotation\IAnnotation;
use umi\form\element\Button;
use umi\form\element\Checkbox;
use umi\form\element\CSRF;
use umi\form\element\Hidden;
use umi\form\element\html5\Color;
use umi\form\element\html5\Date;
use umi\form\element\html5\DateTime;
use umi\form\element\html5\Email;
use umi\form\element\html5\Month;
use umi\form\element\html5\Number;
use umi\form\element\html5\Phone;
use umi\form\element\html5\Range;
use umi\form\element\html5\Search;
use umi\form\element\html5\Time;
use umi\form\element\html5\Url;
use umi\form\element\html5\Week;
use umi\form\element\IElement;
use umi\form\element\MultiCheckbox;
use umi\form\element\Password;
use umi\form\element\Radio;
use umi\form\element\Reset;
use umi\form\element\Select;
use umi\form\element\Submit;
use umi\form\element\Text;
use umi\form\element\Textarea;
use umi\form\exception\OutOfBoundsException;
use umi\form\fieldset\Collection;
use umi\form\fieldset\Fieldset;
use umi\form\fieldset\IFieldset;
use umi\form\Form;
use umi\form\IEntityFactory;
use umi\form\IFormEntity;
use umi\i18n\TLocalizable;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика элементов формы.
 * Создает обычные элементы формы, а также группы полей и коллекции.
 */
class EntityFactory implements IEntityFactory, IFactory
{

    use TFactory;

    /**
     * @var array $elementTypes типы поддерживаемых элементов
     */
    public $elementTypes = [
        Button::TYPE_NAME        => 'umi\form\element\Button',
        Checkbox::TYPE_NAME      => 'umi\form\element\Checkbox',
        Hidden::TYPE_NAME        => 'umi\form\element\Hidden',
        MultiCheckbox::TYPE_NAME => 'umi\form\element\MultiCheckbox',
        Password::TYPE_NAME      => 'umi\form\element\Password',
        Radio::TYPE_NAME         => 'umi\form\element\Radio',
        Submit::TYPE_NAME        => 'umi\form\element\Submit',
        Text::TYPE_NAME          => 'umi\form\element\Text',
        Textarea::TYPE_NAME      => 'umi\form\element\Textarea',
        CSRF::TYPE_NAME          => 'umi\form\element\CSRF',
        Reset::TYPE_NAME         => 'umi\form\element\Reset',
        Select::TYPE_NAME        => 'umi\form\element\Select',
        /*
         * HTML5
         */
        Color::TYPE_NAME         => 'umi\form\element\html5\Color',
        Date::TYPE_NAME          => 'umi\form\element\html5\Date',
        DateTime::TYPE_NAME      => 'umi\form\element\html5\DateTime',
        Email::TYPE_NAME         => 'umi\form\element\html5\Email',
        Month::TYPE_NAME         => 'umi\form\element\html5\Month',
        Number::TYPE_NAME        => 'umi\form\element\html5\Number',
        Phone::TYPE_NAME         => 'umi\form\element\html5\Phone',
        Range::TYPE_NAME         => 'umi\form\element\html5\Range',
        Search::TYPE_NAME        => 'umi\form\element\html5\Search',
        Time::TYPE_NAME          => 'umi\form\element\html5\Time',
        Url::TYPE_NAME           => 'umi\form\element\html5\Url',
        Week::TYPE_NAME          => 'umi\form\element\html5\Week',
    ];

    /**
     * @var array $annotationTypes типы аннотаций
     */
    public $annotationTypes = [
        'action'     => 'umi\form\annotation\ActionAnnotation',
        'method'     => 'umi\form\annotation\MethodAnnotation',
        'label'      => 'umi\form\annotation\LabelAnnotation',
        'filters'    => 'umi\form\annotation\FilterAnnotation',
        'validators' => 'umi\form\annotation\ValidatorAnnotation',
        'required'   => 'umi\form\annotation\RequiredAnnotation',
    ];

    /**
     * @var array $fieldsetTypes типы поддерживаемых наборов элементов
     */
    public $fieldsetTypes = [
        Form::TYPE_NAME       => 'umi\form\Form',
        Fieldset::TYPE_NAME   => 'umi\form\fieldset\Fieldset',
        Collection::TYPE_NAME => 'umi\form\fieldset\Collection',
    ];

    /**
     * {@inheritdoc}
     */
    public function createEntities(array $config)
    {
        $entities = [];

        foreach ($config as $name => $entity) {
            $name = (string) $name;
            $entities[$name] = $this->createEntity($name, $entity);
        }

        return $entities;
    }

    /**
     * {@inheritdoc}
     */
    public function createEntity($name, array $config)
    {
        $type = $this->getEntityType($config);

        $attributes = isset($config['attributes']) ? $config['attributes'] : [];
        $options = isset($config['options']) ? $config['options'] : [];

        if (isset($this->elementTypes[$type])) {
            $entity = $this->createElementEntity($type, $name, $attributes, $options);
        } elseif (isset($this->fieldsetTypes[$type])) {
            $elements = isset($config['elements']) ? $this->createEntities($config['elements']) : [];

            $entity = $this->createFieldsetEntity($type, $name, $attributes, $options, $elements);
        } else {
            throw new OutOfBoundsException($this->translate(
                'Form entity type "{type}" have not supported.',
                ['type' => $type]
            ));
        }

        unset($config['type']);
        unset($config['attributes']);
        unset($config['options']);
        unset($config['elements']);

        $this->initEntity($entity, $config);

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function createForm(array $config)
    {
        $name = isset($config['name']) ? strtolower($config['name']) : '';
        unset($config['name']);

        $config['type'] = Form::TYPE_NAME;

        return $this->createEntity($name, $config);
    }

    /**
     * Создает элемент формы.
     * @param string $type тип элемента
     * @param string $name имя элемента
     * @param array $attributes аттрибуты
     * @param array $options опции
     * @return IElement
     */
    protected function createElementEntity($type, $name, array $attributes, array $options)
    {
        return $this->createInstance(
            $this->elementTypes[$type],
            [$name, $attributes, $options],
            ['umi\form\element\IElement']
        );
    }

    /**
     * Создает группу полей формы.
     * @param string $type тип группы полей
     * @param string $name имя группы полей
     * @param array $attributes аттрибуты
     * @param array $options опции
     * @param array $elements элементы, содержащиеся в группе
     * @return IFieldset
     */
    protected function createFieldsetEntity($type, $name, array $attributes, array $options, array $elements = [])
    {
        return $this->createInstance(
            $this->fieldsetTypes[$type],
            [$name, $attributes, $options, $elements],
            ['umi\form\fieldset\IFieldset']
        );
    }

    /**
     * Инициализирует элемент формы с помощью аннотаций.
     * @param IFormEntity $entity элемент
     * @param array $config конфигурация
     */
    protected function initEntity(IFormEntity $entity, array $config)
    {
        foreach ($config as $name => $value) {
            $this->createAnnotation($name, $value)
                ->transform($entity);
        }
    }

    /**
     * Возвращает экземпляр аннотации заданного типа.
     * @param string $type тип аннотации
     * @param mixed $value значение
     * @throws OutOfBoundsException если аннотация заданного типа не существует
     * @return IAnnotation
     */
    protected function createAnnotation($type, $value)
    {
        if (!isset($this->annotationTypes[$type])) {
            throw new OutOfBoundsException($this->translate(
                'Invalid annotation type "{type}".',
                ['type' => $type]
            ));
        }

        return $this->createInstance(
            $this->annotationTypes[$type],
            [$value],
            ['umi\form\annotation\IAnnotation']
        );
    }

    /**
     * Определяет тип элемента на основе его конфигурации.
     * @param array $config конфигурация
     * @return string тип элемента
     */
    protected function getEntityType(array $config)
    {
        $type = Text::TYPE_NAME;
        if (isset($config['type'])) {
            $type = $config['type'];
        } elseif (isset($config['elements'])) {
            $type = Fieldset::TYPE_NAME;
        }

        return $type;
    }

}