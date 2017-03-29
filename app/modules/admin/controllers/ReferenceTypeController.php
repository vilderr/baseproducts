<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 09.03.17
 * Time: 12:19
 */

namespace app\modules\admin\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

use app\modules\admin\components\Controller;
use app\modules\admin\models\ReferenceType;

/**
 * Class ReferenceTypeController
 * @package app\modules\admin\controllers
 */
class ReferenceTypeController extends Controller
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $query = ReferenceType::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new ReferenceType model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return string | \yii\web\Response
     */
    public function actionCreate()
    {
        $model = new ReferenceType();
        $model->loadDefaultValues();

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ReferenceType model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return string | \yii\web\Response
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
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
     * @return string | \yii\web\Response
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['/admin/reference-type']);
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
}