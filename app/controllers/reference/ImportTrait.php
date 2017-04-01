<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 27.03.17
 * Time: 10:01
 */

namespace app\controllers\reference;

use Yii;
use yii\helpers\Json;
use app\models\reference\import\YmlOffers;
use app\models\reference\import\YmlTree;
use app\components\helpers\FileHelper;
use app\components\Yml;
use app\models\reference\import\Import;

/**
 * Class ImportTrait
 * @package app\controllers\reference
 */
trait ImportTrait
{
    public function actionImport($type, $reference_id)
    {
        $model = new Import();
        $reference = $this->findReferenceByType($type, $reference_id);
        $request = Yii::$app->request;
        $interval = 30;
        $arErrors = [];

        if ($request->isPost && $request->isAjax) {
            $post = $request->post();
            $yml = new Yml();

            if (isset($post['NS']) && is_array($post['NS'])) {
                $NS = $post['NS'];
            } else {
                $NS = [
                    'STEP'         => 0,
                    'REFERENCE_ID' => $reference->id,
                    'FILES'        => Import::getFiles(),
                ];
            }

            if (is_array($NS['FILES']) && !empty($NS['FILES'])) {
                if ($NS['STEP'] < 1) {
                    Yii::$app->session->remove('SECTION_MAP');
                    YmlTree::truncateTable();
                    YmlOffers::truncateTable();

                    if (Yml::checkFileIsYml($NS['FILES'][0], $NS)) {
                        $NS["STEP"]++;
                    } else {
                        $arErrors[] = "Загруженный файл не является валидным YML";
                    }
                } elseif ($NS['STEP'] < 2) {

                    $fp = fopen($NS['FILES'][0], 'rb');
                    if ($yml->readToBase($fp, $NS, $interval)) {
                        $NS['STEP']++;
                    }
                    fclose($fp);
                } elseif ($NS["STEP"] < 3) {

                    $obYmlImport = new Import();
                    $obYmlImport->initialize($NS);

                    $result = $obYmlImport->importMetaData(1);
                    if ($result === true) {
                        $NS['YMLHASH'] = time();//метка текущего импорта
                        $NS['STEP']++;
                    } else {
                        $arErrors[] = "Ошибка во время импорта метаданных";
                    }
                } elseif ($NS['STEP'] < 4) {

                    $obYmlImport = new Import();
                    $obYmlImport->initialize($NS);
                    $start_time = time();
                    $obYmlImport->importSectionsData(Yii::$app->session->get('SECTION_MAP', []));
                    $result = $obYmlImport->importElements($start_time, $interval);
                    $counter = 0;
                    foreach ($result as $key => $value) {
                        $NS['DONE'][$key] += $value;
                        $counter += $value;
                    }

                    if (!$counter) {
                        $NS["STEP"]++;
                        $NS['LAST_YML_ID'] = 0;
                    }
                } elseif ($NS['STEP'] < 5) {
                    $obYmlImport = new Import();
                    $start_time = time();
                    $obYmlImport->initialize($NS);
                    $result = $obYmlImport->deleteElements($start_time, $interval);
                    $counter = 0;
                    foreach ($result as $key => $value) {
                        $NS['DONE'][$key] += $value;
                        $counter += $value;
                    }

                    if (!$counter) {
                        $NS["STEP"]++;
                        $NS['LAST_YML_ID'] = 0;
                    }
                } elseif ($NS['STEP'] < 6) {
                    $obYmlImport = new Import();
                    $start_time = time();
                    $obYmlImport->initialize($NS);
                    $result = $obYmlImport->deleteProperties($start_time, $interval);
                    $counter = 0;
                    foreach ($result as $key => $value) {
                        $NS['DONE'][$key] += $value;
                        $counter += $value;
                    }

                    if (!$counter) {
                        $NS["STEP"]++;
                        $NS['LAST_YML_ID'] = 0;
                    }
                }
            } else {
                $arErrors[] = 'Папка с фидами пуста!';
            }

            foreach ($arErrors as $strError) {
                echo '<p class="bg-danger text-danger small notify">' . $strError . '</p>';
            }

            if (count($arErrors) == 0) {
                if ($NS["STEP"] < 6) {

                    echo '<p class="bg-primary text-primary small notify">Импортирую <b>"' . FileHelper::getFileName($NS['FILES'][0]) . '"</b></p>';

                    if ($NS['STEP'] < 2) {
                        $file_position = 0;
                        $file_size = intval($NS['file_size']);
                        if (isset($NS['file_position']))
                            $file_position = intval($NS['file_position']);

                        $percent = round(($file_position * 100) / $file_size);

                        echo '<p class="bg-info text-info small notify">Импортирую XML файл в базу данных - ' . $percent . '%</p>';
                    } elseif ($NS['STEP'] < 3) {
                        echo '<p class="bg-info text-info small notify">Импортирую метаданные каталога</p>';
                    } elseif ($NS['STEP'] < 4) {

                        if ((!isset($NS["DONE"])) && $NS["YML_ELEMENTS_ROOT"]) {
                            $NS['DONE'] = [
                                "ALL" => YmlTree::find()->where(['parent_id' => intval($NS["YML_ELEMENTS_ROOT"])])->count(),// общее количество товаров
                                "ADD" => 0, // добавлено
                                "UPD" => 0, // обновлено
                                "DEL" => 0, // удалено
                                "ERR" => 0, // с ошибками
                                "CRC" => 0, // выбрано из базы
                                "NAV" => 0, // исключено отсутствующих товаров
                                "DPR" => 0, // удалено отсутствующих свойств
                            ];
                        }

                        echo '<p class="bg-info text-info small notify">';
                        echo '<b>Загружаю товары в базу</b><br><br>';

                        foreach ($NS['DONE'] as $key => $value) {
                            echo '<span><label>' . Yii::t('app/reference', $key) . ':</label> ' . $value . '</span><br>';
                        }

                        echo '</p>';
                    } elseif ($NS['STEP'] < 5) {
                        echo '<p class="bg-info text-info small notify">';
                        echo '<b>Удаляю отсутствующие товары</b><br><br>';

                        foreach ($NS['DONE'] as $key => $value) {
                            echo '<span><label>' . Yii::t('app/reference', $key) . ':</label> ' . $value . '</span><br>';
                        }

                        echo '</p>';
                    } elseif ($NS['STEP'] < 6) {
                        echo '<p class="bg-info text-info small notify">';
                        echo '<b>Удаляю отсутствующие товары</b><br><br>';

                        foreach ($NS['DONE'] as $key => $value) {
                            echo '<span><label>' . Yii::t('app/reference', $key) . ':</label> ' . $value . '</span><br>';
                        }

                        echo '</p>';
                    }

                    echo '<script>DoNext(' . Json::encode(['NS' => $NS]) . ')</script>';

                } else {
                    unlink($NS['FILES'][0]);
                    array_shift($NS['FILES']);
                    if (count($NS['FILES']) > 0) {
                        $NS = [
                            'STEP'         => 0,
                            'REFERENCE_ID' => $reference->id,
                            'FILES'        => $NS['FILES'],
                        ];

                        echo '<script>DoNext(' . Json::encode(['NS' => $NS]) . ')</script>';
                    } else {
                        echo '<p class="bg-info text-info small notify"><b>Импорт успешно завершен</b></p>';
                        echo '<script>EndImport();</script>';
                    }
                }
            } elseif (empty($NS['FILES'])) {
                echo '<script>EndImport();</script>';
            } else {
                unlink($NS['FILES'][0]);
                array_shift($NS['FILES']);
                if (count($NS['FILES']) > 0) {
                    $NS = [
                        'STEP'         => 0,
                        'REFERENCE_ID' => $reference->id,
                        'FILES'        => $NS['FILES'],
                    ];

                    echo '<script>DoNext(' . Json::encode(['NS' => $NS]) . ')</script>';
                } else {
                    echo '<p class="bg-info text-info small notify"><b>Импорт успешно завершен</b></p>';
                    echo '<script>EndImport();</script>';
                }
            }

            Yii::$app->end();
        }

        return $this->render('import', [
            'model'     => $model,
            'reference' => $reference,
        ]);
    }
}