<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 04.04.17
 * Time: 22:30
 */

use app\assets\distribution\DistributionAsset;

/**
 * @var $this yii\web\View
 * @var $model app\models\distribution\Distribution
 */
DistributionAsset::register($this);
$this->title = 'Распределение элементов';
$this->params['breadcrumbs'][] = ['label' => $model->reference->referenceType->name, 'url' => ['/reference', 'type' => $model->reference->referenceType->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{reference}: Distribution', ['reference' => $model->reference->name])];
?>
<div id="distribution_result_div"></div>
<?
$this->registerJs('StartDistribution(' . $model->id . ');');
?>
