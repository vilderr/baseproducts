<?php

namespace app\controllers;

use app\models\distribution\DistributionPart;
use app\models\distribution\DistributionSearch;
use Yii;
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

    public function actionCreate($type, $reference_id)
    {
        $reference = $this->findReferenceByType($type, $reference_id);
        $model = new Distribution(['reference_id' => $reference->id]);
        $model->loadDefaultValues();
        $partModel = new DistributionPart();
        $parts = [];

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {

            if ($model->save()) {
                foreach (Yii::$app->request->post($partModel->formName()) as $ID => $arPart) {
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

    public function actionUpdate($type, $reference_id, $id)
    {
        $reference = $this->findReferenceByType($type, $reference_id);
        $model = $this->findModel($id);
        $parts = DistributionPart::find()->where(['distribution_id' => $model->id])->indexBy('id')->all();

        return $this->render('update', [
            'reference' => $reference,
            'model'     => $model,
            'parts'     => $parts,
        ]);
    }

    public function actionProcess()
    {

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
