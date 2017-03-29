<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 21.03.17
 * Time: 18:53
 * @var $this yii\web\View
 * @var $model app\models\reference\Reference
 * @var $referenceType app\models\reference\ReferenceType
 * @var $properties app\models\reference\ReferenceProperty[]
 */

$this->title = Yii::t('app/reference', '{model-name}: Editing', ['model-name' => $model->name]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/reference', 'Reference Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $referenceType->name, 'url' => ['view', 'id' => $referenceType->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model'         => $model,
    'referenceType' => $referenceType,
    'properties'    => $properties,
]) ?>