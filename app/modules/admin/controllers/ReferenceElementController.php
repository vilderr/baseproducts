<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 11.03.17
 * Time: 16:18
 */

namespace app\modules\admin\controllers;

use app\modules\admin\models\ReferenceElementProperty;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\admin\components\Controller;
use app\modules\admin\models\Reference;
use app\modules\admin\models\ReferenceElement;
use app\modules\admin\models\ReferenceSection;
use app\modules\admin\models\search\ReferenceElementSearch;

/**
 * Class ReferenceElementController
 * @package app\modules\admin\controllers
 */
class ReferenceElementController extends Controller
{
    /**
     * @var Reference
     */
    public $reference;

    /**
     * @var ReferenceElement
     */
    public $elementModel;
    /**
     * @var ReferenceSection
     */
    public $sectionModel;
    /**
     * @var ReferenceElementProperty
     */
    public $elementPropertyModel;

    /**
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

    public function init()
    {
        parent::init();

        $params = Yii::$app->request->queryParams;

        $this->reference = $this->findReference($params['reference_id']);

        $this->elementModel = new ReferenceElement(['reference_id' => $this->reference->id, 'reference' => $this->reference]);
        $this->sectionModel = new ReferenceSection(['reference_id' => $this->reference->id]);
        $this->elementPropertyModel = new ReferenceElementProperty(['reference_id' => $this->reference->id]);
    }

    /**
     * @param int $reference_section_id
     * @return string
     */
    public function actionIndex($reference_section_id = 0)
    {
        if (($section = $this->sectionModel->findOne($reference_section_id)) === null) {
            $section = $this->sectionModel;
        }

        $searchModel = new ReferenceElementSearch(['reference_section_id' => $reference_section_id]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'section'      => $section,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $reference_section_id
     * @return string|\yii\web\Response
     */
    public function actionCreate($reference_section_id = 0)
    {
        $section = $this->findSection($reference_section_id);
        $element = $this->elementModel;
        $element->loadDefaultValues();
        $element->reference_section_id = $reference_section_id;
        $post = Yii::$app->request->post();
        $properties = $element->getProperties();
        $formName = $this->elementPropertyModel->formName();
        //echo'<pre>';print_r($properties); echo '</pre>';

        if (Yii::$app->request->isPost && $element->load($post)) {
            if ($element->validate()) {


                if (isset($post[$formName])) {
                    foreach ($post[$formName] as $PID => $propertyData) {
                        $this->collectPropertyFromPost($properties, $PID, $propertyData);
                    }

                    foreach ($properties as $property) {
                        /**
                         * @var $property \app\modules\admin\models\ReferenceElementProperty[]
                         */
                        if ($property && !Model::validateMultiple($property)) {
                            foreach ($property as $prop) {
                                $this->errors[] = implode('<br>', $prop->getErrors('value'));
                            }
                        }
                    }
                }

                if (empty($this->errors)) {
                    $element->_properties = $properties;
                    $element->save(false);

                    return $this->redirect([
                        'index',
                        'reference_id'         => $element->reference->id,
                        'reference_section_id' => $element->reference_section_id,
                    ]);

                } else {
                    Yii::$app->session->setFlash('danger', implode('<br />', $this->errors));
                }
            }
        }

        return $this->render('create', [
            'element'    => $element,
            'section'    => $section,
            'properties' => $properties,
        ]);
    }

    /**
     * @param $id
     * @param int $reference_section_id
     * @return string|\yii\web\Response
     */
    public function actionUpdate($id, $reference_section_id = 0)
    {
        $element = $this->findElement($id);
        $section = $this->findSection($reference_section_id);
        $properties = $element->getProperties();

        $post = Yii::$app->request->post();
        $formName = $this->elementPropertyModel->formName();

        if(Yii::$app->request->isPost && $element->load(Yii::$app->request->post()))
        {
            if ($element->validate()) {

                if (isset($post[$formName])) {
                    foreach ($post[$formName] as $PID => $propertyData) {
                        $this->collectPropertyFromPost($properties, $PID, $propertyData);
                    }

                    foreach ($properties as $property) {
                        /**
                         * @var $property \app\modules\admin\models\ReferenceElementProperty[]
                         */
                        if ($property && !Model::validateMultiple($property)) {
                            foreach ($property as $prop) {
                                $this->errors[] = implode('<br>', $prop->getErrors('value'));
                            }
                        }
                    }
                }

                if (empty($this->errors)) {
                    $element->_properties = $properties;
                    $element->save(false);

                    return $this->redirect([
                        'index',
                        'reference_id'         => $element->reference->id,
                        'reference_section_id' => $element->reference_section_id,
                    ]);

                } else {
                    Yii::$app->session->setFlash('danger', implode('<br />', $this->errors));
                }
            }
        }

        return $this->render('update', [
            'element'    => $element,
            'section'    => $section,
            'properties' => $properties,
        ]);
    }

    /**
     * @param $properties array
     * @param $PID integer
     * @param $data array
     */
    private function collectPropertyFromPost(&$properties, $PID, $data)
    {
        foreach ($data as $id => $property) {
            if (!$property['value']) {
                unset($properties[$PID][$id]);
                continue;
            }

            if (isset($properties[$PID][$id])) {
                /**
                 * @var $model \app\modules\admin\models\ReferenceElementProperty
                 */
                $model = $properties[$PID][$id];
            } else {
                $model = new ReferenceElementProperty([
                    'reference_id' => $this->reference->id,
                    'property_id'  => $PID,
                ]);
            }

            $model->load($data, $id);
            $properties[$PID][$id] = $model;
        }
    }

    /**
     * @param $PID
     * @param $post
     * @return ReferenceElementProperty|ReferenceElementProperty[]
     */
    protected function loadNewPropertyFromPost($PID, $post)
    {
        $property = null;

        if (ArrayHelper::keyExists('value', $post)) {
            if(strlen($post['value']) > 0)
                $property = new ReferenceElementProperty(['reference_id' => $this->elementModel->reference_id, 'property_id' => $PID, 'value' => $post['value']]);
        } else {
            $property = [];
            foreach ($post as $oneProp) {
                if (strlen($oneProp['value']) > 0) {
                    $property[] = new ReferenceElementProperty(['reference_id' => $this->elementModel->reference_id, 'property_id' => $PID, 'value' => $oneProp['value']]);
                }
            }
        }

        return $property;
    }

    public function actionDelete($id)
    {
        $element = $this->findElement($id);
        $element->delete();

        return $this->redirect(['index', 'reference_id' => $this->reference->id, 'reference_section_id' => $element->reference_section_id]);
    }

    /**
     * @param $id
     * @return Reference
     * @throws NotFoundHttpException
     */
    protected function findReference($id)
    {
        if(($reference = Reference::findOne($id)) !== null)
        {
            return $reference;
        }
        else
        {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @param $id
     * @return ReferenceSection
     */
    protected function findSection($id)
    {
        if (($section = $this->sectionModel->findOne($id)) === null) {
            $section = $this->sectionModel;
        }

        return $section;
    }

    /**
     * @param $id
     * @param ReferenceElement $model
     * @return ReferenceElement
     * @throws NotFoundHttpException
     */
    protected function findElement($id)
    {
        if(($element = $this->elementModel->findOne($id)) !== null)
        {
            $element->reference = $this->reference;
            return $element;
        }
        else
        {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}