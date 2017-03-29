<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 05.03.17
 * Time: 14:20
 * @var $this yii\web\View
 */

$this->title = Yii::t('app', 'admin-panel-title');

use app\models\reference\Reference;
use app\models\reference\ReferenceElement;

$arDbElement = ReferenceElement::findOne(182225439);
$properties = $arDbElement ? $arDbElement->initProperties() : (new ReferenceElement(['reference_id' => 3]))->initProperties();
//$model = Reference::findOne(3);
//$properties = $model->getProperties()->indexBy('xml_id')->all();

echo '<pre>'; print_r($properties); echo '</pre>';