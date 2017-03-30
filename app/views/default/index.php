<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 05.03.17
 * Time: 14:20
 * @var $this yii\web\View
 */

use app\models\reference\import\YmlTree;

$this->title = Yii::t('app', 'admin-panel-title');
set_time_limit(0);
YmlTree::truncateTable();
$file = Yii::getAlias('@app/yml_files/bonprix.xml');
$reader = new XMLReader();
$reader->open($file);
$element = [];

while ($reader->read()) {

    if ($reader->nodeType == XMLReader::DOC_TYPE)
        continue;

    if ($reader->nodeType == XMLReader::ELEMENT) {
        $element = new YmlTree();
        $element->name = $reader->localName;
        $element->depth = $reader->depth;
        if ($reader->hasValue) {
            $element->value = $reader->value;
        }
        if ($reader->hasAttributes) {
            $attributes = [];
            while ($reader->moveToNextAttribute()) {
                $attributes[$reader->name] = $reader->value;
            }
            $element->attrs = serialize($attributes);
            $reader->moveToElement();
        }
        $element->save(false);
    }
}
$reader->close();

//echo $attrs;
//echo '<pre>'; print_r($node); echo '</pre>';