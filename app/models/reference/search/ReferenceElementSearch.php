<?php

namespace app\models\reference\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use app\models\reference\ReferenceProperty;
use app\models\reference\ReferenceElement;
use app\models\reference\ReferenceSection;
use app\models\reference\Reference;

/**
 * ReferenceElementSearch represents the model behind the search form about `app\models\reference\ReferenceElement`.
 */
class ReferenceElementSearch extends ReferenceElement
{
    /**
     * @var Reference
     */
    public $reference;
    /**
     * @var bool
     */
    public $subsections;
    /**
     * @var int|array
     */
    public $section_id;

    public $property = [];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'reference_id', 'section_id', 'created_at', 'updated_at', 'active', 'sort', 'detail_picture', 'preview_picture', 'discount'], 'integer'],
            [['name', 'code', 'xml_id', 'currency', 'shop'], 'safe'],
            [['price', 'oldprice'], 'number'],
            ['subsections', 'boolean'],
            ['property', 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ReferenceElement::find()->where(['reference_element.reference_id' => $this->reference->id])->with('reference');

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'pagination' => [
                'pageSizeLimit' => [1, 500],
                'pageSize'      => Yii::$app->request->getQueryParam('page-size', Yii::$app->session->get('page-size', 20)),
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->subsections) {
            if (($section = ReferenceSection::findOne($this->section_id)) !== null) {
                $query->andWhere([
                    'reference_element.reference_section_id' => $section->getChildrenIDs(),
                ]);
            }
        } else {
            $query->andWhere([
                'reference_element.reference_section_id' => $this->section_id,
            ]);
        }

        foreach ($this->property as $PID => $property) {
            if (strlen($property) > 0) {
                $subQuery = (new Query())->from('reference_element_property')->where(['property_id' => $PID]);
                $query->leftJoin(['P' . $PID => $subQuery], 'P' . $PID . '.element_id = reference_element.id');

                switch ($this->reference->properties[$PID]->type) {
                    case ReferenceProperty::TYPE_STRING:
                        $query->andWhere(['or like', 'P' . $PID . '.value', explode(' ', trim($property))]);
                        break;
                    case ReferenceProperty::TYPE_INTEGER:
                    case ReferenceProperty::TYPE_LINK_ELEMENT:
                    case ReferenceProperty::TYPE_LINK_SECTION:
                        $query->andWhere(['P' . $PID . '.value' => $property]);
                        break;
                }
            }
        }

        $query->andFilterWhere([
            //'reference_element.id'       => $this->id,
            'reference_element.active'   => $this->active,
            'reference_element.price'    => $this->price,
            'reference_element.oldprice' => $this->oldprice,
            'reference_element.discount' => $this->discount,
        ]);

        if ($this->name) {
            $query->andFilterWhere(['or like', 'reference_element.name', explode(' ', $this->name)]);
        }

        $query->andFilterWhere(['like', 'reference_element.code', $this->code])
            ->andFilterWhere(['like', 'reference_element.xml_id', $this->xml_id])
            ->andFilterWhere(['like', 'reference_element.currency', $this->currency])
            ->andFilterWhere(['like', 'reference_element.shop', $this->shop]);

        return $dataProvider;
    }
}
