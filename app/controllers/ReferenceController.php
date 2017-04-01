<?php

namespace app\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\models\reference\Reference;
use app\models\reference\ReferenceType;
use app\models\reference\ReferenceSection;
use app\models\reference\ReferenceProperty;
use app\models\reference\search\ReferenceSearch;
use app\components\Controller;
use app\controllers\reference\SectionTrait;
use app\controllers\reference\ElementTrait;
use app\controllers\reference\ImportTrait;

/**
 * ReferenceController implements the CRUD actions for Reference model.
 */
class ReferenceController extends Controller
{
    use SectionTrait;
    use ElementTrait;
    use ImportTrait;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {

        $b = [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete'                => ['POST'],
                    'load-property-line'    => ['POST'],
                    'load-property-setting' => ['POST'],
                    'reference-chooser'     => ['POST'],
                    'delete-section'        => ['POST'],
                    'delete-element'        => ['POST'],
                ],
            ],
        ];

        return ArrayHelper::merge(parent::behaviors(), $b);
    }

    /**
     * Lists all Reference models.
     * @return mixed
     */
    public function actionIndex($type)
    {
        $referenceType = $this->findReferenceType($type);
        $searchModel = new ReferenceSearch(['reference_type_id' => $referenceType->id]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'   => $searchModel,
            'dataProvider'  => $dataProvider,
            'referenceType' => $referenceType,
        ]);
    }

    /**
     * Deletes an existing Reference model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $reference_type_id = $model->referenceType->id;
        $model->delete();

        return $this->redirect(['/reference-type/view', 'id' => $reference_type_id]);
    }

    /**
     * @return string
     */
    public function actionLoadPropertyLine()
    {
        $model = new ReferenceProperty;
        $model->loadDefaultValues();
        $uid = substr(md5(uniqid("", true)), 0, 5);

        return $this->renderAjax('_property_line', [
            'model' => $model,
            'PID'   => $uid,
        ]);
    }

    /**
     * @return string
     */
    public function actionLoadPropertySetting()
    {
        $params = Yii::$app->request->post();
        $type = $params['type'];
        $property_id = $params['property_id'];

        if (ArrayHelper::keyExists($type, ReferenceProperty::getTypes())) {
            $model = new ReferenceProperty;

            return $this->renderAjax('_property_setting', [
                'model'       => $model,
                'property_id' => $property_id,
                'type'        => $type,
            ]);
        }

        return '';
    }

    /**
     * render dropdown for reference chooser
     * @return string
     */
    public function actionReferenceChooser()
    {
        $params = Yii::$app->request->post();
        $type = $params['type'];
        $name = $params['name'];
        $id = $params['id'];

        $referenceList = $this->getReferenceList(['reference_type_id' => $type]);

        return Html::dropDownList($name, null, $referenceList, ['class' => 'form-control', 'id' => $id]);
    }

    /**
     * Finds the Reference model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Reference the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Reference::find()->where(['id' => $id])->limit(1)->with('referenceType')->one()) !== null) {
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
            '' => '-- Справочник --',
        ];
        foreach ($references as $reference) {
            $key = ArrayHelper::getValue($reference, 'id');
            $value = ArrayHelper::getValue($reference, 'name');

            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * @param $id
     * @return ReferenceType
     * @throws NotFoundHttpException
     */
    public function findReferenceType($id)
    {
        if (($model = ReferenceType::findOne($id)) !== null) {
            return $model;
        } else {
            throw new  NotFoundHttpException('The requested page does not exist.');
        }
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
     * @param $reference_id
     * @param $id
     * @return ReferenceSection
     * @throws NotFoundHttpException
     */
    protected function findSection($reference_id, $id)
    {
        $id = intval($id);
        if ($id == 0) {
            $section = new ReferenceSection();
            $section->id = 0;
            $section->name = 'Верхний уровень';
            $section->reference_id = $reference_id;
        } elseif (($section = ReferenceSection::find()->limit(1)->where(['id' => $id])->forReference($reference_id)->one()) === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $section;
    }
}
