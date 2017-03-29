<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 18.03.17
 * Time: 17:47
 */

namespace app\modules\admin\controllers;

use Yii;
use yii\bootstrap\Progress;
use yii\helpers\Json;
use app\modules\admin\components\Controller;
use app\modules\admin\models\ElementImport;
use app\modules\admin\models\YmlFileImport;
use app\modules\admin\models\YmlFile;
use app\modules\admin\models\YmlImport;

/**
 * Class ElementImportController
 * @package app\modules\admin\controllers
 */
class ElementImportController extends Controller
{
    public function actionIndex()
    {
        @set_time_limit(0);
        $model = new ElementImport;
        $request = Yii::$app->request;
        $params = Yii::$app->request->queryParams;
        $arErrors = [];
        $arMessages = [];

        if ($request->isPost && $request->isPost) {
            $post = $request->post();

            $INTERVAL = intval(Yii::$app->request->getQueryParam('INTERVAL', 30));
            if ($INTERVAL == 0)
                $INTERVAL = 30;

            $ymlFile = new YmlFileImport();

            if (isset($post['NS']) && is_array($post['NS'])) {
                $NS = $post['NS'];
            } else {
                $NS = [
                    'STEP'          => 0,
                    'REFERENCE_ID'  => $params['REFERENCE_ID'],
                    'URL_DATA_FILE' => $params['URL_DATA_FILE'],
                    'ACTION'        => $params['ACTION'],
                ];
            }

            if ($NS['URL_DATA_FILE']) {
                if ($NS['STEP'] < 1) {

                    YmlFileImport::dropTables();

                    if(!$NS['REFERENCE_ID'])
                        $arErrors[] = 'Справочник для товаров не выбран';

                    if (YmlFileImport::checkFileIsYml($NS['URL_DATA_FILE'], $NS)) {
                        $NS["STEP"]++;
                    } else {
                        $arErrors[] = "Загруженный файл не является валидным YML";
                    }

                } elseif ($NS['STEP'] < 2) {

                    if (YmlFileImport::createTables()) {
                        $NS['STEP']++;
                    } else
                        $arErrors[] = "Ошибка во время создания временных таблиц";

                } elseif ($NS['STEP'] < 3) {

                    $fp = fopen($NS["URL_DATA_FILE"], 'rb');
                    if ($ymlFile->readToBase($fp, $NS, $INTERVAL)) {
                        $NS['STEP']++;
                    }
                    fclose($fp);

                } elseif ($NS["STEP"] < 4) {

                    $obYmlImport = new YmlImport;
                    $obYmlImport->initialize($NS);


                    $result = $obYmlImport->importMetaData(1);
                    if ($result === true) {
                        $NS['YMLHASH'] = time();//метка текущего импорта
                        $NS['STEP']++;
                    } else {
                        $arErrors[] = "Ошибка во время импорта метаданных";
                    }

                } elseif ($NS['STEP'] < 5) {

                    if((!isset($NS["DONE"])) && $NS["YML_ELEMENTS_ROOT"])
                    {
                        $NS['DONE'] = [
                            "ALL" => 0, // общее количество товаров
                            "ADD" => 0, // добавлено
                            "UPD" => 0, // обновлено
                            "DEL" => 0, // удалено
                            "DEA" => 0, // деактивировано
                            "ERR" => 0, // с ошибками
                            "CRC" => 0, // выбрано из базы
                            "NAV" => 0, // исключено отсутствующих товаров
                        ];

                        $NS['DONE']['ALL'] = YmlFile::find()->where(['parent_id' => intval($NS["YML_ELEMENTS_ROOT"])])->count();
                    }

                    $obYmlImport = new YmlImport;
                    $obYmlImport->initialize($NS);
                    $start_time = time();

                    $result = $obYmlImport->importElements($start_time, $INTERVAL);
                    $counter = 0;
                    foreach ($result as $key => $value) {
                        $NS["DONE"][$key] += $value;
                        $counter += $value;
                    }

                    echo '<pre>'; print_r($NS); echo '</pre>';


                    if(!$counter)
                        $NS["STEP"]++;
                }
            } else {
                $arErrors[] = "Файл импорта не выбран";
            }

            foreach ($arErrors as $strError) {
                echo '<p class="bg-danger text-danger small notify">' . $strError . '</p>';
            }

            if (count($arErrors) == 0) {

                $percent = 0;

                if ($NS["STEP"] < 5) {

                    $file_position = 0;
                    $file_size = intval($NS['file_size']);
                    if (isset($NS['file_position']))
                        $file_position = intval($NS['file_position']);

                    $percent = round(($file_position * 100) / $file_size);

                    echo Progress::widget([
                        'percent' => $percent,
                        'label'   => 'Импортирую файл в базу данных... (' . $percent . '%)',
                    ]);

                    echo '<script>DoNext(' . Json::encode(['NS' => $NS]) . ')</script>';
                } else {
                    echo '<script>EndImport();</script>';
                }

            } else {
                echo '<script>EndImport();</script>';
            }

            Yii::$app->end();
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }
}