<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 05.03.17
 * Time: 14:20
 * @var $this yii\web\View
 */

$this->title = Yii::t('app', 'Admin Panel');

use app\models\file\File;
use app\models\reference\ReferenceElement;
use  yii\helpers\FileHelper;
$start_time = time();
$interval = 30;

$elements = ReferenceElement::find()->limit(10)->where(['reference_id' => '3', 'detail_picture' => null])->all();
foreach ($elements as $element)
{
    $arFile = File::makeArray($element->picture_src);
    if(is_array($arFile))
    {
        $element->detail_picture = File::saveFile($arFile, 'reference');
    }
    else
    {
        $element->reference_section_id = 30;
    }

    $element->save(false);

    if ($interval > 0 && (time() - $start_time) > $interval)
        break;
}
