<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 05.03.17
 * Time: 14:20
 * @var $this yii\web\View
 */

use app\models\distribution\Distribution;

$this->title = Yii::t('app', 'Admin Panel');

$model = Distribution::find()->limit(1)->where(['id' => 8])->with(['reference', 'activeParts'])->one();

echo '<pre>'; print_r($model); echo '</pre>';