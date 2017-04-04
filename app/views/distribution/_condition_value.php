<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 04.04.17
 * Time: 11:46
 */

/**
 * @var $model app\models\distribution\DistributionPart
 * @var $condition
 * @var $reference app\models\reference\Reference
 * @var $value mixed
 */
use yii\helpers\Html;

$name = 'DistributionPart[' . $model->id . '][data][condition][' . $condition . ']';

switch ($condition) {
    case 'name':
    case 'props':
        echo '&nbsp;::&nbsp;&nbsp;' . Html::textInput($name, $value, ['class' => 'form-control']);
        break;
    case 'section':
        echo '&nbsp;::&nbsp;&nbsp;' . Html::dropDownList($name, null, $reference->getSectionTree(), ['class' => 'form-control']);
        break;
    case 'price':
        echo '&nbsp;::&nbsp;&nbsp;' . Html::input('number', $name . '[from]', null, ['class' => 'form-control']) . '&nbsp;--&nbsp;' . Html::input('number', $name . '[to]', null, ['class' => 'form-control']);
        break;
}
