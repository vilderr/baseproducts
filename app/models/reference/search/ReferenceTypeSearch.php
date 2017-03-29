<?php

namespace app\models\reference\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\reference\ReferenceType;

/**
 * ReferenceTypeSearch represents the model behind the search form about `app\models\reference\ReferenceType`.
 */
class ReferenceTypeSearch extends ReferenceType
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'safe'],
            [['created_at', 'updated_at', 'sort'], 'integer'],
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
        $query = ReferenceType::find();

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
            'sort' => $this->sort,
        ]);

        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
