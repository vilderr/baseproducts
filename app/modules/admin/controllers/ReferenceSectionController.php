<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 10.03.17
 * Time: 11:54
 */

namespace app\modules\admin\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use app\modules\admin\components\Controller;
use app\modules\admin\models\Reference;
use app\modules\admin\models\ReferenceSection;
use app\modules\admin\models\search\ReferenceSectionSearch;

/**
 * Class ReferenceSectionController
 * @package app\modules\admin\controllers
 */
class ReferenceSectionController extends Controller
{
    /**
     * @var Reference
     */
    public $reference;
    /**
     * @var ReferenceSection
     */
    public $sectionModel;

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
        $this->sectionModel = new ReferenceSection(['reference_id' => $this->reference->id]);
    }

    /**
     * @param int $reference_section_id
     * @return string
     */
    public function actionIndex($reference_section_id = 0)
    {
        $section = $this->findSection($reference_section_id);

        $searchModel = new ReferenceSectionSearch(['reference_section_id' => $reference_section_id]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'section'      => $section,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $reference_id
     * @param int $reference_section_id
     * @return string|\yii\web\Response
     */
    public function actionCreate($reference_section_id = 0)
    {
        $section = $this->findSection($reference_section_id);

        $this->sectionModel->loadDefaultValues();
        $this->sectionModel->reference_section_id = $reference_section_id;

        if (!empty(Yii::$app->request->post('ReferenceSection'))) {
            if ($this->sectionModel->load(Yii::$app->request->post())) {

                if ($reference_section_id) {
                    $parent = $this->sectionModel::findOne($reference_section_id);
                    $result = $this->sectionModel->appendTo($parent);
                } else {
                    $result = $this->sectionModel->makeRoot();
                }

                if ($result) {
                    return $this->redirect(['index', 'reference_id' => $this->reference->id, 'reference_section_id' => $this->sectionModel->reference_section_id]);
                }
            }
        }

        return $this->render('create', [
            'model'     => $this->sectionModel,
            'section'   => $section,
        ]);
    }

    /**
     * @param $reference_section_id
     * @return string|\yii\web\Response
     */
    public function actionUpdate($reference_section_id)
    {
        $section = $this->findSection($reference_section_id);

        if(Yii::$app->request->isPost && $section->load(Yii::$app->request->post()))
        {
            $newSect = $section::findOne($section->reference_section_id);

            if($section->reference_section_id > 0)
            {
                if ($section->id == $section->reference_section_id)
                {
                    Yii::$app->session->setFlash('danger', 'Нельзя переместить раздел внутрь себя');

                    return $this->refresh();
                }
                elseif($newSect->isChildOf($section))
                {
                    Yii::$app->session->setFlash('danger', 'Нельзя переместить раздел внутрь своего потомка');

                    return $this->refresh();
                }

                if($section->getOldAttribute('reference_section_id') != $section->reference_section_id)
                {
                    $section->prependTo($newSect);
                }
            }
            elseif($section->getOldAttribute('reference_section_id') != $section->reference_section_id)
            {
                $section->makeRoot();
            }

            if($section->save())
            {
                return $this->redirect(['index', 'reference_id' => $this->reference->id, 'reference_section_id' => $section->reference_section_id]);
            }
        }
        else
        {
            return $this->render('update', [
                'section'   => $section,
            ]);
        }
    }

    /**
     * @param $reference_id
     * @param $id
     * @return \yii\web\Response
     */
    public function actionDelete($id)
    {
        $section = $this->findSection($id);
        $section->deleteWithChildren();

        return $this->redirect(['index', 'reference_id' => $this->reference->id, 'reference_section_id' => $section->reference_section_id]);
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
        if (($section = $this->sectionModel::findOne($id)) === null) {
            $section = $this->sectionModel;
        }

        return $section;
    }
}