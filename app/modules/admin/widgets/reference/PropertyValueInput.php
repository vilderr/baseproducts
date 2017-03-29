<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 16.03.17
 * Time: 16:45
 */

namespace app\modules\admin\widgets\reference;

use app\modules\admin\models\ReferenceElementProperty;
use app\modules\admin\models\ReferenceProp;
use yii\base\Widget;
use yii\helpers\Html;
use app\modules\admin\assets\reference\Property;

class PropertyValueInput extends Widget
{
    /**
     * general options for widget
     * @var array
     */
    public $options = [];
    /**
     * @var \app\modules\admin\models\ReferenceElementProperty
     */
    public $model;
    /**
     * @var \app\modules\admin\models\ReferenceProp
     */
    public $property = null;

    /**
     * Initialize widget.
     */
    public function init()
    {
        parent::init();

        if ($this->model === null)
        {
            $this->model = new ReferenceElementProperty(['reference_id' => $this->property->reference_id, 'property_id' => $this->property->id]);
        }

        if(is_array($this->model) && empty($this->model))
        {
            $this->model = [new ReferenceElementProperty(['reference_id' => $this->property->reference_id, 'property_id' => $this->property->id])];
        }

        $this->initOptions();
    }

    public function run()
    {
        echo Html::beginTag('div', $this->options);
        echo $this->renderLabel() . "\n";
        echo $this->renderValue() . "\n";

        if ($this->property->multiple) {
            echo Html::a('Добавить', null, ['class' => 'btn btn-default add-property-value-input', 'data-container' => '#' . $this->options['id'], 'data-attribute' => 'ReferenceElementProperty['.$this->property->id.'][][value]']);
        }

        echo Html::endTag('div');

        $this->registerPlugin();
    }

    /**
     * Initializes the widget options.
     * This method sets the default values for various options.
     */
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

    protected function renderValue()
    {
        switch ($this->property->type) {
            case ReferenceProp::TYPE_STRING:
            case ReferenceProp::TYPE_INTEGER:
            case ReferenceProp::TYPE_LINK_ELEMENT:
            case ReferenceProp::TYPE_LINK_SECTION:
                return $this->renderDefault($this->property);
                break;
        }
    }

    /**
     * @param $property
     * @return string
     */
    protected function renderDefault(ReferenceProp $property)
    {
        $input_type = ($property->type == ReferenceProp::TYPE_STRING) ? 'string' : 'number';

        if (is_array($this->model))
        {
            return $this->renderModelMultiple($this->model, $input_type);
        }

        return Html::activeInput($input_type, $this->model, '['.$this->property->id.']['.$this->model->id.']value', ['class' => 'form-control', 'id' => null]);
    }

    /**
     * @param $model ReferenceElementProperty[]
     * @param $input_type string
     * @return string
     */
    protected function renderModelMultiple(Array $model, $input_type)
    {
        $html = '';
        foreach ($model as $prop) {
            $html .= Html::activeInput($input_type, $prop, '['.$this->property->id.']['.$prop->id.']value', ['class' => 'form-control', 'id' => null]) . "\n";
        }

        return $html;
    }

    /**
     * register js plugin for this widget
     */
    protected function registerPlugin()
    {
        $view = $this->getView();
        Property::register($view);
    }
}