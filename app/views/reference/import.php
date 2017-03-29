<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 27.03.17
 * Time: 10:10
 */

/**
 * @var $this yii\web\View
 * @var $reference app\models\reference\Reference
 */
use yii\helpers\Html;
use app\assets\reference\ImportAsset;

ImportAsset::register($this);

$this->title = 'Импорт элементов из YML файла';
?>
<div id="import_result_div"></div>
<div class="import-form">
    <?= Html::beginForm(['/reference/import']); ?>
    <?= Html::hiddenInput('reference_type', $reference->referenceType->id, ['id' => 'reference_type_id']) ?>
    <?= Html::hiddenInput('reference_id', $reference->id, ['id' => 'reference_id']) ?>
    <?= Html::submitButton('Запустить', ['class' => 'btn btn-primary', 'id' => 'start_button', 'onclick' => 'StartImport();']); ?>
    <?= Html::a('Остановить', null, ['class' => 'btn btn-default', 'id' => 'stop_button', 'onclick' => 'EndImport();']); ?>
    <?= Html::endForm(); ?>
</div>
<br>
<p class="bg-warning text-warning small notify">Поля выделенные жирным, обязательны для заполнения</p>
