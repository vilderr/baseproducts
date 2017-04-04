<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 02.04.17
 * Time: 21:58
 */

/**
 * @var $this yii\web\View
 * @var $reference app\models\reference\Reference
 */

$this->title = Yii::t('app', 'Distribution Elements');
$this->params['breadcrumbs'][] = ['label' => $reference->referenceType->name, 'url' => ['/reference', 'type' => $reference->referenceType->id]];
$this->params['breadcrumbs'][] = ['label' => $reference->name, 'url' => ['/reference/section', 'type' => $reference->reference_type_id, 'reference_id' => $reference->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/reference', 'Reference Distribution')];