<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 24.03.17
 * Time: 13:39
 */

/**
 * @var $reference app\models\reference\Reference
 * @var $section app\models\reference\ReferenceSection
 * @var $element app\models\reference\ReferenceElement
 * @var $properties app\models\reference\ReferenceElementProperty[]
 */

use yii\widgets\ActiveForm;
use yii\bootstrap\Tabs;
use yii\helpers\Html;

?>
<? $form = ActiveForm::begin([
    'id' => 'reference-element-create-form',
]); ?>

<?
$tabsItems = [
    [
        'label'   => 'Элемент',
        'active'  => true,
        'content' => $this->render('_element_form_field_part', ['form' => $form, 'element' => $element, 'section' => $section]),
        'options' => [
            'id' => 'home-tab',
        ],
    ],
];

if ($reference->catalog) {
    $tabsItems[] = [
        'label'   => 'Каталог',
        'content' => $this->render('_element_form_catalog_part', ['form' => $form, 'element' => $element]),
        'options' => [
            'id' => 'catalog-tab',
        ],
    ];
}

$tabsItems[] = [
    'label'   => 'Свойства',
    'content' => $this->render('_form_properties_part', ['form' => $form, 'properties' => $properties]),
    'options' => [
        'id' => 'property-tab',
    ],
];
?>
<?= Tabs::widget([
    'items' => $tabsItems,
]); ?>
<div class="form-group">
    <?= Html::submitButton($element->isNewRecord ? Yii::t('app/reference', 'create-btn') : Yii::t('app/reference', 'update-btn'), ['class' => 'btn btn-success']) ?>
    <?= Html::a(Yii::t('app/reference', 'cancell-btn'), [Yii::$app->user->getReturnUrl(\yii\helpers\Url::toRoute(['element', 'type' => $reference->reference_type_id, 'reference_id' => $reference->id, 'reference_section_id' => $section->id]))], ['class' => 'btn btn-default']); ?>
</div>
<? ActiveForm::end(); ?>
<p class="bg-warning text-warning small notify"><?= Yii::t('app/reference', 'Sure fields') ?></p>
