<?php

namespace app\controllers;

use app\models\distribution\DistributionPart;
use app\models\distribution\DistributionSearch;
use app\models\distribution\Parser;
use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\reference\Reference;
use app\models\distribution\Distribution;

/**
 * Class DistributionController
 * @package app\controllers
 */
class DistributionController extends \app\components\Controller
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @param $type
     * @param $reference_id
     * @return string
     */
    public function actionIndex($type, $reference_id)
    {
        $reference = $this->findReferenceByType($type, $reference_id);
        $searchModel = new DistributionSearch(['reference_id' => $reference->id]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'reference'    => $reference,
        ]);
    }

    /**
     * @param $type
     * @param $reference_id
     * @return string|\yii\web\Response
     */
    public function actionCreate($type, $reference_id)
    {
        $reference = $this->findReferenceByType($type, $reference_id);
        $model = new Distribution(['reference_id' => $reference->id]);
        $model->loadDefaultValues();
        $partModel = new DistributionPart();
        $parts = [];

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if (($partsPost = Yii::$app->request->post($partModel->formName())) === null) {
                $partsPost = [];
            }
            if ($model->save()) {
                foreach ($partsPost as $ID => $arPart) {
                    if (!isset($arPart['data']['operation'])) {
                        unset($partsPost[$ID]);
                        continue;
                    }

                    $part = new DistributionPart([
                        'active' => $arPart['active'],
                        'data'   => serialize($arPart['data']),
                    ]);
                    $part->link('distribution', $model);
                }

                return $this->redirect(['index', 'type' => $reference->referenceType->id, 'reference_id' => $model->reference_id]);
            }
        }

        return $this->render('create', [
            'reference' => $reference,
            'model'     => $model,
            'parts'     => $parts,
        ]);
    }

    /**
     * @param $type
     * @param $reference_id
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionUpdate($type, $reference_id, $id)
    {
        $reference = $this->findReferenceByType($type, $reference_id);
        $model = $this->findModel($id);
        $partModel = new DistributionPart();
        $parts = DistributionPart::find()->where(['distribution_id' => $model->id])->indexBy('id')->all();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (($partsPost = Yii::$app->request->post($partModel->formName())) === null) {
                $partsPost = [];
            }

            foreach ($partsPost as $ID => $arPart) {
                if (!isset($arPart['data']['operation'])) {
                    unset($partsPost[$ID]);
                    continue;
                }

                if (ArrayHelper::keyExists($ID, $parts)) {
                    $parts[$ID]->active = $arPart['active'];
                    $parts[$ID]->data = serialize($arPart['data']);
                } else {
                    $parts[$ID] = new DistributionPart([
                        'active' => $arPart['active'],
                        'data'   => serialize($arPart['data']),
                    ]);
                }
            }

            foreach ($parts as $ID => $part) {
                if (ArrayHelper::keyExists($ID, $partsPost)) {
                    $part->link('distribution', $model);
                } else {
                    $part->unlink('distribution', $model, true);
                }
            }

            return $this->redirect(['index', 'type' => $reference->referenceType->id, 'reference_id' => $reference->id]);
        }

        return $this->render('update', [
            'reference' => $reference,
            'model'     => $model,
            'parts'     => $parts,
        ]);
    }

    public function actionProcess($id, $run = false)
    {
        $model = Distribution::find()->limit(1)->where(['id' => $id])->with(['reference', 'activeParts'])->one();
        if ($model === null) {
            throw new NotFoundHttpException('Страница не найдена');
        }
        $request = Yii::$app->request;
        $arErrors = [];

        if ($request->isPost && $run == 'Y') {
            $post = $request->post();
            $parser = new Parser();

            if (isset($post['NS']) && is_array($post['NS'])) {
                $NS = $post['NS'];
            } else {
                $NS = [
                    'step'  => 0,
                    'count' => count($model->activeParts),
                    'id'    => $model->id,
                ];
            }

            $start_time = time();
            $parser->init($NS);

            if ($NS['step'] < $NS['count']) {
                echo '<p class="bg-primary text-primary small notify">Обрабатываю итерацию #' . ($NS['step'] + 1) . '</p>';
                $counter = $parser->parseElements($NS['step'], $start_time);

                if (!$counter) {
                    $NS["step"]++;
                    $NS["last_id"] = 0;
                }
                echo '<script>DoNext(' . $model->id . ',' . Json::encode(['NS' => $NS]) . ');</script>';
            } else {
                echo '<p class="bg-primary text-primary small notify">Парсинг завершен</p>';
                echo Html::a('Назад', ['index', 'type' => $model->reference->referenceType->id, 'reference_id' => $model->reference->id], ['class' => 'btn btn-default']);
                echo '<script>EndDistribution();</script>';
            }

            Yii::$app->end();
        }

        return $this->render('process', [
            'model' => $model,
        ]);
    }

    /**
     * @return string
     */
    public function actionAddPart()
    {
        $reference = Reference::findOne(Yii::$app->request->post('reference_id'));
        $model = new DistributionPart();
        $model->loadDefaultValues();

        return $this->renderAjax('_new_part', [
            'model'     => $model,
            'reference' => $reference,
        ]);
    }

    /**
     * @return array|string
     */
    public function actionPartService()
    {
        $action = Yii::$app->request->post('action');

        switch ($action) {
            case 'part_remove':
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                $model = DistributionPart::findOne(Yii::$app->request->post('part_id'));
                if ($model === null || $model->delete()) {
                    $status = 'OK';
                } else {
                    $status = 'ERROR';
                }
                return ['status' => $status];
                break;

            case 'add-condition-line':
                if (($model = DistributionPart::findOne(Yii::$app->request->post('part_id'))) === null) {
                    $model = new DistributionPart(['id' => Yii::$app->request->post('part_id')]);
                }
                return $this->renderAjax('_condition_line', [
                    'reference' => Reference::findOne(Yii::$app->request->post('reference_id')),
                    'model'     => $model,
                    'name'      => null,
                    'value'     => null,
                ]);
                break;

            case 'condition':
                if (($model = DistributionPart::findOne(Yii::$app->request->post('part_id'))) === null) {
                    $model = new DistributionPart(['id' => Yii::$app->request->post('part_id')]);
                }

                return $this->renderAjax('_condition_value', [
                    'model'     => $model,
                    'condition' => Yii::$app->request->post('value'),
                    'reference' => Reference::findOne(Yii::$app->request->post('reference_id')),
                    'value'     => null,
                ]);
                break;

            case 'add-action-line':
                if (($model = DistributionPart::findOne(Yii::$app->request->post('part_id'))) === null) {
                    $model = new DistributionPart(['id' => Yii::$app->request->post('part_id')]);
                }

                return $this->renderAjax('_action_line', [
                    'var'   => Yii::$app->request->post('var'),
                    'model' => $model,
                    'name'  => null,
                    'value' => null,
                ]);
                break;
            case 'operation':
                if (($model = DistributionPart::findOne(Yii::$app->request->post('part_id'))) === null) {
                    $model = new DistributionPart(['id' => Yii::$app->request->post('part_id')]);
                }

                return $this->renderAjax('_operation_value', [
                    'model'     => $model,
                    'operation' => Yii::$app->request->post('value'),
                    'reference' => Reference::findOne(Yii::$app->request->post('reference_id')),
                    'value'     => null,
                ]);
                break;
        }
    }

    /**
     * @param $type
     * @param $reference_id
     * @param $id
     * @return \yii\web\Response
     */
    public function actionDelete($type, $reference_id, $id)
    {
        $reference = $this->findReferenceByType($type, $reference_id);
        $model = Distribution::findOne($id);
        $model->delete();

        return $this->redirect(['index', 'type' => $reference->referenceType->id, 'reference_id' => $reference->id]);
    }

    /**
     * @param $type
     * @param $id
     * @return Reference
     * @throws NotFoundHttpException
     */
    protected function findReferenceByType($type, $id)
    {
        if (($model = Reference::find()->limit(1)->where(['reference_type_id' => $type, 'id' => $id])->with('referenceType')->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не найдена');
        }
    }

    /**
     * @param $id
     * @return Distribution
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Distribution::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не найдена');
        }
    }
}
