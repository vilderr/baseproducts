<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 25.03.17
 * Time: 14:58
 */

namespace app\widgets\reference;

use yii\base\Widget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\reference\ReferenceElementProperty;
use app\models\reference\ReferenceProperty;
use app\assets\reference\PropertyValueAsset;

class PropertyValue extends Widget
{
    public $options = [];

    /**
     * @var \app\models\reference\ReferenceProperty
     */
    public $property;
    public $properties = [];
    /**
     * @var ActiveForm
     */
    public $form;

    public function init()
    {
        parent::init();

        if (($this->properties = $this->property->elementProperties) == []) {
            $this->properties[] = new ReferenceElementProperty(['property_id' => $this->property->id]);
        }

        $this->initOptions();
    }

    public function run()
    {
        echo Html::beginTag('div', $this->options);
        echo $this->renderLabel() . "\n";
        echo $this->renderValues() . "\n";

        if ($this->property->multiple) {
            echo Html::a('Добавить', null, ['class' => 'btn btn-default add-property-value-input', 'data-container' => '#' . $this->options['id'], 'data-attribute' => 'ReferenceElementProperty[' . $this->property->id . '][][value]']);
        }
        echo Html::endTag('div');

        $this->registerPlugin();
    }

    public function initOptions()
    {
        $this->options = array_merge([
            'id' => 'property-' . $this->property->id . '-container',
        ], $this->options);

        $defaultCss = [
            'widget' => 'form-group',
        ];

        if ($this->property->multiple) {
            $defaultCss['widget'] = 'form-group multiple';
        }

        Html::addCssClass($this->options, $defaultCss);
    }

    /**
     * @return string
     */
    public function renderLabel()
    {
        return Html::tag('label', $this->property->name);
    }

    protected function renderValues()
    {
        switch ($this->property->type) {
            case ReferenceProperty::TYPE_STRING:
            case ReferenceProperty::TYPE_INTEGER:
            case ReferenceProperty::TYPE_LINK_ELEMENT:
            case ReferenceProperty::TYPE_LINK_SECTION:
                return $this->renderDefault($this->property);
                break;
        }
    }

    /**
     * @param $property
     * @return string
     */
    protected function renderDefault(ReferenceProperty $property)
    {
        $input_type = ($property->type == ReferenceProperty::TYPE_STRING) ? 'string' : 'number';

        $html = '';
        foreach ($this->properties as $property)
        {
            $html .= $this->form->field($property, '['.$this->property->id.']['.$property->id.']value')->label(false);
        }

        return $html;
    }

    /**
     * register js plugin for this widget
     */
    protected function registerPlugin()
    {
        $view = $this->getView();
        PropertyValueAsset::register($view);
    }
}