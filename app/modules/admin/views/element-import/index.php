<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 18.03.17
 * Time: 17:51
 * @var $this yii\web\View
 * @var $model app\modules\admin\models\ElementImport
 */

use yii\helpers\Html;
use app\modules\admin\models\ElementImport;
use app\modules\admin\assets\reference\ElementImport as ElementImportAsset;
use app\modules\admin\widgets\reference\ReferenceChooser;

ElementImportAsset::register($this);
$this->title = 'Импорт элементов из YML файла';
?>
<div id="import_result_div"></div>
<div class="row">
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Настройки импорта</h3>
            </div>
            <div class="panel-body">
            <?= Html::beginForm('/admin/element-import/process');?>
                <div class="form-group required">
                    <?= Html::label($model->attributeLabels()['file']); ?>
                    <?= Html::activeDropDownList($model, 'file', ElementImport::getFiles(), ['class' => 'form-control', 'id' => 'url_data_file']); ?>
                </div>
                <?
                echo ReferenceChooser::widget([
                    'reference_type_id_name' => $model->formName() . '[reference_type]',
                    'reference_id_name'      => $model->formName() . '[reference_id]',
                    'reference_id_select_id' => 'reference_id_select_id',
                    'reference_id'           => 87,
                    'request_url'            => '/admin/reference/reference-chooser',
                    'header'                 => 'Выбор справочника',
                    'options'                => [
                        'class' => 'required',
                    ],
                ]);
                ?>
                <div class="form-group">
                    <?= Html::label($model->attributeLabels()['action']); ?>
                    <?= Html::activeDropDownList($model, 'action', ElementImport::getActions(), ['class' => 'form-control']); ?>
                </div>
                <div class="form-group">
                    <?= Html::label($model->attributeLabels()['interval']); ?>
                    <?= Html::activeTextInput($model, 'interval', ['class' => 'form-control', 'id' => 'interval', 'value' => 30]); ?>
                </div>
            </div>
            <div class="panel-footer">
                <?=Html::submitButton('Запустить', ['class' => 'btn btn-primary', 'id' => 'start_button', 'onclick' => 'StartImport();']);?>
                <?=Html::a('Остановить', null, ['class' => 'btn btn-default', 'id' => 'stop_button', 'onclick' => 'EndImport();']);?>
            </div>
            <?= Html::endForm();?>
        </div>
    </div>
</div>
<p class="bg-warning text-warning small notify">Поля выделенные жирным, обязательны для заполнения</p>




