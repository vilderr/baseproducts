<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 03.04.17
 * Time: 11:51
 */

/**
 * @var $this yii\web\View
 * @var $form \yii\widgets\ActiveForm
 * @var $model app\models\distribution\Distribution
 * @var $reference app\models\reference\Reference
 * @var $parts app\models\distribution\DistributionPart[]
 */

use app\assets\distribution\DistributionAsset;
use yii\helpers\Json;

DistributionAsset::register($this);
$params = [
    'id'           => 'parts-list',
    'url'          => '/distribution/add-part',
    'reference_id' => $reference->id,
];
?>
    <div id="<?= $params['id'] ?>">
        <?
        foreach ($parts as $ID => $part) {
            echo $this->render('_new_part', [
                'model'     => $part,
                'reference' => $reference,
            ]);
        }
        ?>
    </div>
    <hr class="xs">
    <p class="text-center"><?= \yii\bootstrap\Html::a(Yii::t('app/distribution', 'Add Part'), 'javascript:void(0)', ['class' => 'btn btn-info', 'id' => 'add-new-part', 'onclick' => '$.addPart(' . Json::encode($params) . ');']) ?></p>
<?
$options = Json::encode([
    'addBtn'      => 'add-new-part',
    'url'         => '/distribution/add-part',
    'referenceId' => $reference->id,
]);
?>