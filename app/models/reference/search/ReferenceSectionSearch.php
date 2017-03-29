<?php

namespace app\models\reference\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\reference\ReferenceSection;

/**
 * ReferenceSectionSearch represents the model behind the search form about `app\models\reference\ReferenceSection`.
 */
class ReferenceSectionSearch extends ReferenceSection
{
    /**
     * @var int
     */
    public $active = 1;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'reference_id', 'reference_section_id', 'created_at', 'updated_at', 'active', 'sort', 'lft', 'rgt', 'depth', 'tree'], 'integer'],
            [['name', 'code', 'xml_id'], 'safe'],
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
        $query = ReferenceSection::find()->with('reference');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'reference_id' => $this->reference_id,
            'reference_section_id' => $this->reference_section_id,
            'active' => $this->active,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
