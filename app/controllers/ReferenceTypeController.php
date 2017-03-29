<?php

namespace app\controllers;


use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\reference\ReferenceType;
use app\models\reference\search\ReferenceTypeSearch;
use app\components\Controller;
use app\models\reference\Reference;
use app\models\reference\search\ReferenceSearch;
use app\models\reference\ReferenceProperty;

/**
 * ReferenceTypeController implements the CRUD actions for ReferenceType and Reference models.
 */
class ReferenceTypeController extends Controller
{
    /**
     * @inheritdoc
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
     * Lists all ReferenceType models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ReferenceTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ReferenceType model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $searchModel = new ReferenceSearch(['reference_type_id' => $model->id]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('view', [
            'model'        => $model,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new ReferenceType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ReferenceType();
        $model->loadDefaultValues();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ReferenceType model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ReferenceType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * @param $reference_type_id
     * @return string|\yii\web\Response
     */
    public function actionCreateReference($reference_type_id)
    {
        $referenceType = $this->findModel($reference_type_id);
        $model = new Reference();
        $model->loadDefaultValues();
        $arProperty = [];

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if (Yii::$app->request->post('ReferenceProperty')) {
                foreach (Yii::$app->request->post('ReferenceProperty') as $PID => $prop) {
                    if (!$prop['name'])
                        continue;

                    $property = new ReferenceProperty();
                    $property->load(Yii::$app->request->post('ReferenceProperty'), $PID);
                    if (isset($prop['delete']) && $prop['delete']) {
                        $property->delete = true;
                    } else {
                        if (!$property->validate()) {
                            $this->errors[] = $property->getErrors();
                        }
                    }
                    $arProperty[$PID] = $property;
                }
            }

            if (empty($this->errors)) {
                $model->arProperty = $arProperty;
                if ($model->save(false)) {
                    return $this->redirect(['view', 'id' => $model->referenceType->id]);
                }
            } else {
                foreach ($this->errors[0] as $key => $error) {
                    $this->flash('error', $error);
                }
            }
        }

        return $this->render('/reference/create', [
            'model'         => $model,
            'referenceType' => $referenceType,
            'properties'    => $arProperty,
        ]);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionUpdateReference($id)
    {
        $model = $this->findReference($id);
        $arProperty = $model->properties;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if (Yii::$app->request->post('ReferenceProperty')) {
                foreach (Yii::$app->request->post('ReferenceProperty') as $PID => $prop) {
                    if (!$prop['name'])
                        continue;

                    if (ArrayHelper::keyExists($PID, $model->properties)) {
                        $property = $model->properties[$PID];
                    } else {
                        $property = new ReferenceProperty();
                    }

                    $property->load(Yii::$app->request->post('ReferenceProperty'), $PID);
                    if (isset($prop['delete']) && $prop['delete']) {
                        $property->delete = true;
                    } else {
                        if (!$property->validate()) {
                            $this->errors[] = $property->getErrors();
                        }
                    }

                    $arProperty[$PID] = $property;
                }
            }


            if (empty($this->errors)) {
                $model->arProperty = $arProperty;
                if ($model->save(false) && !Yii::$app->request->post('apply'))
                {
                    return $this->redirect(['view', 'id' => $model->referenceType->id]);
                }
                else
                {
                    foreach ($arProperty as $PID => $property) {
                        if ($property->delete) {
                            unset($arProperty[$PID]);
                        }
                    }
                }
            } else {
                foreach ($this->errors[0] as $key => $error) {
                    $this->flash('error', $error);
                }
            }
        }

        return $this->render('/reference/update', [
            'model'         => $model,
            'referenceType' => $model->referenceType,
            'properties'    => $arProperty,
        ]);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     */
    public function actionDeleteReference($id)
    {
        $model = $this->findReference($id);
        $reference_type_id = $model->referenceType->id;
        $model->delete();

        return $this->redirect(['view', 'id' => $reference_type_id]);
    }

    /**
     * Finds the ReferenceType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return ReferenceType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ReferenceType::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the Reference model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return Reference the loaded model
     * @throws NotFoundHttpException
     */
    protected function findReference($id)
    {
        if (($model = Reference::find()->where(['id' => $id])->limit(1)->with(['referenceType', 'properties'])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
