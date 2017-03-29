<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 09.03.17
 * Time: 12:49
 */

namespace app\modules\admin\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use app\modules\admin\components\Controller;
use app\modules\admin\models\Reference;
use app\modules\admin\models\ReferenceType;
use app\modules\admin\helpers\Table;
use app\modules\admin\models\ReferenceProp;

/**
 * Class ReferenceController
 * @package app\modules\admin\controllers
 */
class ReferenceController extends Controller
{
    /**
     * array for collection controller errors
     * @var array
     */
    public $errors = [];

    /**
     * @inheritdoc
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
     * @param $type
     * @return string
     */
    public function actionIndex($type)
    {
        $className = ReferenceType::className();
        $referenceType = $this->findModel($type, $className);

        $query = Reference::find()->where(['reference_type_id' => $referenceType->id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('index', [
            'referenceType' => $referenceType,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Reference model.
     * If creation is successful, will be created tables for Sections / Elements and  browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate($type)
    {
        $className = ReferenceType::className();
        $referenceType = $this->findModel($type, $className);

        $reference = new Reference();
        $reference->loadDefaultValues();

        $arProperty = [];

        if (Yii::$app->request->isPost && $reference->load(Yii::$app->request->post())) {

            if ($referenceValid = $reference->validate()) {
                $post = Yii::$app->request->post();

                if (isset($post['property'])) {
                    foreach ($post['property'] as $uid => $prop) {

                        $property = new ReferenceProp();
                        $property->attributes = $prop;

                        $arProperty[$uid] = $property;

                        $propValid = $property->validate();
                        if (!$propValid) {
                            foreach ($property->getErrors() as $key => $error) {
                                $this->errors[$key] = implode('<br />', $error);
                            }
                        }
                    }
                }
            }

            if (empty($this->errors) && $referenceValid) {

                $reference->save(false);
                Table::createTables($reference);

                foreach ($arProperty as $property) {
                    /**
                     * @var $property \app\modules\admin\models\ReferenceProp
                     */
                    $property->reference_id = $reference->id;
                    $property->save(false);
                }

                return $this->redirect(['index', 'type' => $reference->reference_type_id]);

            } else {
                Yii::$app->session->setFlash('danger', implode('<br />', $this->errors));
            }
        }

        return $this->render('create', [
            'reference'     => $reference,
            'referenceType' => $referenceType,
            'arProperty'    => $arProperty,
            'errors'        => $this->errors,
        ]);
    }

    public function actionProp()
    {
        if(Yii::$app->request->isAjax)
        {
            $model = new ReferenceProp();
            $model->loadDefaultValues();

            $params = [
                'model' => $model,
            ];

            return $this->renderAjax('prop', $params);
        }
    }

    public function actionPropSetting($property_id, $type)
    {
        if(Yii::$app->request->isAjax && ArrayHelper::keyExists($type, ReferenceProp::getTypes()))
        {
            return $this->renderAjax('prop-setting', [
                'property_id' => $property_id,
                'type'        => $type
            ]);
        }
    }
    
    /**
     * render dropdown for reference chooser
     * @param string $type
     * @param string $name
     * @return string
     */
    public function actionReferenceChooser($type, $name, $id)
    {
        if(Yii::$app->request->isAjax)
        {
            $referenceList = $this->getReferenceList(['reference_type_id' => $type]);
        
            return Html::dropDownList($name, null, $referenceList, ['class' => 'form-control', 'id' => $id]);
        }
    }

    /**
     * update current reference
     * update current properties
     * add new properties for current reference
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionUpdate($id)
    {
        $className = Reference::className();
        $reference = $this->findModel($id, $className);

        $arProperty = $reference->properties;

        if (Yii::$app->request->isPost && $reference->load(Yii::$app->request->post())) {
            if ($referenceValid = $reference->validate()) {
                $post = Yii::$app->request->post();

                if (isset($post['property'])) {
                    //echo '<pre>';print_r($post['property']);echo'</pre>';
                    foreach ($post['property'] as $uid => $prop) {
                        /**
                         * @var $property \app\modules\admin\models\ReferenceProp
                         */
                        if (ArrayHelper::keyExists($uid, $arProperty))//current prop
                        {
                            $property = $arProperty[$uid];

                        } else {//new prop
                            $property = new ReferenceProp();
                        }
                        unset($arProperty[$uid]);

                        $property->load($post['property'], $uid);
                        $property->

                        $arProperty[$uid] = $property;

                        $propValid = $property->validate();
                        if (!$propValid) {
                            foreach ($property->getErrors() as $key => $error) {
                                $this->errors[$key] = implode('<br />', $error);
                            }
                        }
                    }
                }
            }

            if (empty($this->errors) && $referenceValid) {
                $reference->save(false);

                foreach ($arProperty as $property) {
                    /**
                     * @var $property \app\modules\admin\models\ReferenceProp
                     */
                    $property->reference_id = $reference->id;
                    $property->save(false);

                }

                return $this->redirect(['index', 'type' => $reference->reference_type_id]);
            } else {
                Yii::$app->session->setFlash('danger', implode('<br />', $this->errors));
            }
        }

        return $this->render('update', [
            'reference'  => $reference,
            'arProperty' => $arProperty,
            'errors'     => $this->errors,
        ]);
    }

    /**
     * Deletes an existing Reference model.
     * If deletion is successful, will be deleted Section / Elements tables and the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $className = Reference::className();
        $reference = $this->findModel($id, $className);

        $reference->delete();

        return $this->redirect(['index', 'type' => $reference->reference_type_id]);
    }

    /**
     * Finds the ReferenceType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return ReferenceType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $class)
    {
        if (($model = $class::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
     * finds references by condition and make new array for dropdown list
     * @param array $condition
     * @return array
     */
    protected function getReferenceList(array $condition)
    {
        $references = Reference::find()->select(['id', 'name'])->where($condition)->asArray()->all();

        $result = [
            '' => '-- Справочник --'
        ];
        foreach ($references as $reference) {
            $key = ArrayHelper::getValue($reference, 'id');
            $value = ArrayHelper::getValue($reference, 'name');

            $result[$key] = $value;
        }
        
        return $result;
    }
}