<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 23.03.17
 * Time: 11:11
 */

namespace app\controllers\reference;

use Yii;
use app\models\reference\ReferenceSection;
use app\models\reference\search\ReferenceSectionSearch;

/**
 * Class SectionTrait
 * @package app\controllers\reference
 */
trait SectionTrait
{
    /**
     * @param $type
     * @param $reference_id
     * @param int $reference_section_id
     * @return mixed
     */
    public function actionSection($type, $reference_id, $reference_section_id = 0)
    {
        $reference = $this->findReferenceByType($type, $reference_id);
        $section = $this->findSection($reference->id, $reference_section_id);

        $searchModel = new ReferenceSectionSearch(['reference_id' => $reference_id, 'reference_section_id' => $reference_section_id]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('section/index', [
            'model'        => $reference,
            'section'      => $section,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $type
     * @param $reference_id
     * @param int $reference_section_id
     * @return mixed
     */
    public function actionCreateSection($type, $reference_id, $reference_section_id = 0)
    {
        $reference = $this->findReferenceByType($type, $reference_id);
        $section = $this->findSection($reference->id, $reference_section_id);

        $model = new ReferenceSection();
        $model->loadDefaultValues();
        $model->setAttribute('reference_section_id', $section->id);
        $model->reference_id = $reference->id;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->reference_section_id > 0) {
                $parent = $model::findOne($model->reference_section_id);
                $result = $model->appendTo($parent);
            } else {
                $result = $model->makeRoot();
            }

            if ($result) {
                $redirect = (Yii::$app->request->post('apply') != 'Y') ? 'section' : 'update-section';
                $id = (Yii::$app->request->post('apply') != 'Y') ? $model->reference_section_id : $model->id;
                return $this->redirect([
                    $redirect,
                    'type'                 => $model->reference->reference_type_id,
                    'reference_id'         => $model->reference->id,
                    'reference_section_id' => $id,
                ]);
            }
        }

        return $this->render('section/create', [
            'reference' => $reference,
            'model'     => $model,
            'section'   => $section,
        ]);
    }

    /**
     * @param $type
     * @param $reference_id
     * @param $reference_section_id
     * @return mixed
     */
    public function actionUpdateSection($type, $reference_id, $reference_section_id)
    {
        /* @var $section \app\models\reference\ReferenceSection */
        $reference = $this->findReferenceByType($type, $reference_id);
        $section = $this->findSection($reference->id, $reference_section_id);

        if ($section->load(Yii::$app->request->post()) && $section->validate()) {
            $parent = $section::findOne($section->reference_section_id);

            if ($section->reference_section_id > 0) {
                if ($section->id == $section->reference_section_id) {
                    Yii::$app->session->setFlash('danger', 'Нельзя переместить раздел внутрь себя');
                    return $this->refresh();
                } elseif ($parent->isChildOf($section)) {
                    Yii::$app->session->setFlash('danger', 'Нельзя переместить раздел внутрь своего потомка');
                    return $this->refresh();
                }

                if ($section->getOldAttribute('reference_section_id') != $section->reference_section_id) {
                    $section->prependTo($parent);
                }
            } else {
                if ($section->isNewRecord)
                    $section->makeRoot();
            }

            if ($section->save(false)) {
                if (Yii::$app->request->post('apply') != 'Y')
                    return $this->redirect(['section', 'type' => $reference->referenceType->id, 'reference_id' => $reference->id, 'reference_section_id' => $section->reference_section_id]);
            }
        }

        return $this->render('section/update', [
            'reference' => $reference,
            'section'   => $section,
        ]);
    }

    /**
     * @param $type
     * @param $reference_id
     * @param $reference_section_id
     * @return mixed
     */
    public function actionDeleteSection($type, $reference_id, $reference_section_id)
    {
        $reference = $this->findReferenceByType($type, $reference_id);
        $section = $this->findSection($reference->id, $reference_section_id);
        $section->deleteWithChildren();

        return $this->redirect(['section',
            'type'                 => $reference->referenceType->id,
            'reference_id'         => $reference->id,
            'reference_section_id' => $section->reference_section_id,
        ]);
    }
}