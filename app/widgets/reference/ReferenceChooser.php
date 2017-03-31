<?php
namespace app\widgets\reference;

use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\models\reference\Reference;
use app\models\reference\ReferenceType;
use app\assets\reference\ReferenceChooserAsset;

/**
 * Class ReferenceChooser
 * @package app\modules\admin\widgets
 */
class ReferenceChooser extends Widget
{
    /**
     * text for label
     * label not render if it's null
     * @var string 
     */
    public $header;
    /**
     * advanced options for label
     * @var array
     */
    public $headerOptions;
    /**
     * name for reference_type_id dropdown
     * @var string
     */
    public $reference_type_id_name = 'reference_type_id';
    /**
     * choosed value for reference_type_id dropdown
     * @var string default null
     */
    public $reference_type_id = null;
    /**
     * name for reference_id dropdown
     * @var string
     */
    public $reference_id_name = 'reference_id';
    /**
     * @var string
     */
    public $reference_id_select_id = 'reference_select_id';
    /**
     * choosed value for reference_id dropdown
     * @var integer default null
     */
    public $reference_id = null;
    /**
     * url for request new dropdown html
     * @var string
     */
    public $request_url;
    /**
     * advanced  options for widget
     * @var array
     */
    public $options = [];
    /**
     * unique string for per widget
     * @var string
     */
    protected $uid;

    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();

        if($this->reference_id !== null)
        {
            $reference = Reference::findOne(['id' => $this->reference_id]);
            $this->reference_type_id = $reference['reference_type_id'];
        }
        
        $this->uid = preg_replace ('/[^a-zA-Z0-9\s]/', '', $this->reference_id_name);
        
        $this->initOptions();

        echo Html::beginTag('div', $this->options) . "\n"; //start widget box
        echo $this->renderHeader() . "\n";
        echo Html::beginTag('div', ['class' => 'row']) . "\n"; //start lists
        echo Html::beginTag('div', ['class' => 'col-xs-6']);
        echo $this->renderReferenceTypeChooser() . "\n";
        echo Html::endTag('div');
        echo Html::beginTag('div', ['class' => 'col-xs-6', 'id' => 'reference_chooser_'.$this->uid]);
        echo $this->renderReferenceChooser() . "\n";
        echo Html::endTag('div');
    }

    /**
     * Renders the widget.
     */
    public function run() {
        echo "\n" . Html::endTag('div'); //end lists
        echo "\n" . Html::endTag('div'); //end widget box
        $this->registerPlugin('chooser');
    }

    /**
     * Initializes the widget options.
     * This method sets the default values for various options.
     */
    public function initOptions()
    {
        Html::addCssClass($this->options, ['widget' => 'form-group']);
    }

    /**
     * renders widget label
     * @return string|null
     */
    protected function renderHeader()
    {
        if($this->header !== null)
        {
            Html::addCssClass($this->headerOptions, ['widget' => 'chooser-label']);
            return Html::tag('label', $this->header, $this->headerOptions);
        }

        return null;
    }

    /**
     * renders referenceType dropdown list
     * @return string
     */
    protected function renderReferenceTypeChooser() {
        //echo '<pre>';print_r($this);echo'</pre>';
        return Html::dropDownList($this->reference_type_id_name, $this->reference_type_id, $this->getReferenceTypes(), ['class' => 'form-control chooser', 'data-name' => $this->reference_id_name, 'data-id' => $this->reference_id_select_id, 'data-url' => $this->request_url, 'data-target' => '#reference_chooser_'.$this->uid]);
    }

    /**
     * renders reference dropdown list
     * @return string
     */
    protected function renderReferenceChooser() {
        return Html::dropDownList($this->reference_id_name, $this->reference_id, $this->getReferences(), ['class' => 'form-control', 'id' => $this->reference_id_select_id]);
    }

    /**
     * find all reference types and make new array for dropdown list
     * @return array
     */
    protected function getReferenceTypes() {
        $result = [
            '' => '-- Тип справочника --',
        ];
        $types  = ReferenceType::find()->select(['id', 'name'])->asArray()->all();

        foreach ($types as $type) {
            $key   = ArrayHelper::getValue($type, 'id');
            $value = ArrayHelper::getValue($type, 'name');

            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getReferences()
    {
        $result = [
            '' => '-- Справочник --',
        ];

        $references = Reference::find()->select(['id', 'name'])->where(['reference_type_id' => $this->reference_type_id])->asArray()->all();

        foreach ($references as $reference) {
            $key = ArrayHelper::getValue($reference, 'id');
            $value = ArrayHelper::getValue($reference, 'name');

            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * Registers a specific reference plugin
     * @param string $name the name of reference js plugin
     */
    protected function registerPlugin($name) {
        $view = $this->getView();
        ReferenceChooserAsset::register($view);
    }

}