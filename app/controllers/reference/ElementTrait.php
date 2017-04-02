<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 23.03.17
 * Time: 11:25
 */

namespace app\controllers\reference;

use app\models\reference\ReferenceElementProperty;
use Yii;
use app\models\reference\ReferenceElement;
use app\models\reference\ReferenceSection;
use app\models\reference\search\ReferenceElementSearch;
use yii\bootstrap\Dropdown;
use yii\bootstrap\Html;
use yii\web\NotFoundHttpException;

trait ElementTrait
{
    /**
     * @param $type
     * @param $reference_id
     * @param int $reference_section_id
     * @return mixed
     */
    public function actionElement($type, $reference_id, $reference_section_id = 0)
    {
        Yii::$app->user->setReturnUrl(Yii::$app->request->url);

        $reference = $this->findReferenceByType($type, $reference_id);
        $section = $this->findSection($reference->id, $reference_section_id);

        if (Yii::$app->request->isAjax && Yii::$app->request->post('LOAD_TREE') == 'Y') {
            return Html::dropDownList('section_id', 0, $section->getTree(), ['class' => 'form-control']) . "&nbsp";
        }

        if (Yii::$app->request->isPost && Yii::$app->request->post('group-action') == 'Y') {
            $action = Yii::$app->request->post('action');
            $attributes = [];
            switch ($action) {
                case 'DEA':
                case 'ACT':
                    $attributes = ['active' => $action == 'ACT' ? 1 : 0];
                    break;
                case 'MOV':
                    $attributes = ['reference_section_id' => Yii::$app->request->post('section_id')];
                    break;
            }

            ReferenceElement::updateAll($attributes, ['reference_id' => $reference->id, 'id' => Yii::$app->request->post('selection')]);
        }

        $searchModel = new ReferenceElementSearch(['reference' => $reference, 'section_id' => $section->id]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('element/index', [
            'reference'    => $reference,
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
    public function actionCreateElement($type, $reference_id, $reference_section_id = 0)
    {
        $reference = $this->findReferenceByType($type, $reference_id);
        $section = $this->findSection($reference->id, $reference_section_id);

        $model = new ReferenceElement();
        $model->loadDefaultValues();
        $model->setAttribute('reference_id', $reference->id);
        $model->setAttribute('reference_section_id', $section->id);
        $properties = $model->initProperties();
        $arProperty = [];

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if (Yii::$app->request->post('ReferenceElementProperty') !== null) {
                $data = Yii::$app->request->post('ReferenceElementProperty');

                foreach ($data as $PID => $props) {
                    foreach ($props as $ID => $prop) {
                        if (isset($properties[$PID]->elementProperties[$ID])) {
                            $property = $properties[$PID]->elementProperties[$ID];
                        } else {
                            $property = new ReferenceElementProperty(['property_id' => $PID]);
                        }

                        $property->load($props, $ID);
                        $arProperty[$PID][] = $property;
                    }
                }

                $model->arProperty = $arProperty;
            }

            if ($model->save(false)) {
                return $this->redirect([
                    'element',
                    'type'                 => $reference->referenceType->id,
                    'reference_id'         => $reference->id,
                    'reference_section_id' => $model->reference_section_id,
                ]);
            }
        }

        return $this->render('element/create', [
            'reference'  => $reference,
            'section'    => $section,
            'element'    => $model,
            'properties' => $properties,
        ]);
    }

    /**
     * @param $type
     * @param $reference_id
     * @param $id
     * @return mixed
     */
    public function actionUpdateElement($type, $reference_id, $id)
    {
        $reference = $this->findReferenceByType($type, $reference_id);
        $element = $this->findElement($reference->id, $id);
        $section = $this->findSection($reference->id, $element->reference_section_id);
        $properties = $element->initProperties();
        $arProperty = [];


        if ($element->load(Yii::$app->request->post()) && $element->validate()) {
            if (Yii::$app->request->post('ReferenceElementProperty') !== null) {
                $data = Yii::$app->request->post('ReferenceElementProperty');

                foreach ($data as $PID => $props) {
                    foreach ($props as $ID => $prop) {
                        if (isset($properties[$PID]->elementProperties[$ID])) {
                            $property = $properties[$PID]->elementProperties[$ID];
                        } else {
                            $property = new ReferenceElementProperty(['property_id' => $PID]);
                        }

                        $property->load($props, $ID);
                        $arProperty[$PID][] = $property;
                    }
                }

                $element->arProperty = $arProperty;
            }

            if ($element->save(false)) {
                return $this->redirect([
                    'element',
                    'type'                 => $reference->referenceType->id,
                    'reference_id'         => $reference->id,
                    'reference_section_id' => $element->reference_section_id,
                ]);
            }
        }


        return $this->render('element/update', [
            'reference'  => $reference,
            'element'    => $element,
            'section'    => $section,
            'properties' => $properties,
        ]);
    }

    /**
     * @param $type
     * @param $reference_id
     * @param $id
     * @return mixed
     */
    public function actionDeleteElement($type, $reference_id, $id)
    {
        $reference = $this->findReferenceByType($type, $reference_id);
        $element = $this->findElement($reference->id, $id);

        if ($element->delete()) {
            return $this->redirect(['element', 'type' => $element->reference->reference_type_id, 'reference_id' => $element->reference->id, 'reference_section_id' => $element->reference_section_id]);
        }
    }

    /**
     * @param $reference_id
     * @param $id
     * @return ReferenceElement|array|null
     * @throws NotFoundHttpException
     */
    public function findElement($reference_id, $id)
    {
        if (($model = ReferenceElement::find()->limit(1)->where(['id' => $id])->forReference($reference_id)->one()) !== null) {
            return $model;
        } else {
            throw  new  NotFoundHttpException('The requested page does not exist.');
        }
    }
}