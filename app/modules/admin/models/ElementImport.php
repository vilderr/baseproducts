<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 18.03.17
 * Time: 18:18
 */

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;
use yii\base\Widget;
use yii\bootstrap\Modal;
use app\modules\admin\helpers\FileHelper;
use yii\helpers\ArrayHelper;

/**
 * Class ElementImport
 * @package app\modules\admin\models
 */
class ElementImport extends Model
{
    const DELETE = 'DEL';
    const DEACTIVATE = 'DEA';
     /**
     * default path for yml files
     * @var string
     */
    public static $filePath = '@app/yml_files';
    /**
     * yml file name
     * @var string
     */
    public $file;
    /**
     * @var
     */
    public $action;
    /**
     * @var integer
     */
    public $interval;
    /**
     * @var integer
     */
    public $reference_id;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['file', 'reference_id'], 'required'],
            ['interval', 'integer'],
            ['interval', 'default', 'value' => 30],
            ['reference_id', 'exist', 'targetClass' => Reference::className(), 'targetAttribute' => ['reference_id' => 'id']]
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'file' => 'Файл для импорта',
            'action' => 'Действия с элементами отсутствующими в файле импорта',
            'interval' => 'Шаг импорта'
        ];
    }

    /**
     * @return array
     */
    public static function getFiles()
    {
        $files = FileHelper::findFiles(Yii::getAlias(static::$filePath));

        $arFiles = ArrayHelper::index($files, function ($element) {
            return FileHelper::getFileName($element);
        });

        return array_flip($arFiles);
    }

    /**
     * @return array
     */
    public static function getActions()
    {
        return [
            self::DELETE     => 'Удалить',
            self::DEACTIVATE => 'Деактивировать',
        ];
    }
}