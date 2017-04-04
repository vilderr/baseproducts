<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 04.04.17
 * Time: 13:58
 */

use yii\helpers\Html;
use app\models\distribution\DistributionPart;

/**
 * @var $model app\models\distribution\DistributionPart
 * @var $operation string
 * @var $reference app\models\reference\Reference
 * @var $value mixed
 */

$name = $model->formName() . '[' . $model->id . '][data][operation][' . $operation . ']';

switch ($operation) {
    case 'active':
        echo '&nbsp;::&nbsp;&nbsp;' . Html::dropDownList($name, $value, DistributionPart::getActiveArray(), ['class' => 'form-control']);
        break;
    case 'section':
        echo '&nbsp;::&nbsp;&nbsp;' . Html::dropDownList($name, $value, $reference->getSectionTree(), ['class' => 'form-control']);
        break;

}