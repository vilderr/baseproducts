<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 10.03.17
 * Time: 13:26
 */

namespace app\modules\admin\models\search;

use Yii;
use yii\base\Model;
use app\modules\admin\components\ActiveDataProvider;

/**
 * Class ReferenceSectionSearch
 * @package app\modules\admin\models\search
 */
class ReferenceSectionSearch extends Model
{
    const IN_ACTIVE = 0;
    const ACTIVE = 1;
    /**
     * @var \app\modules\admin\models\ReferenceSection
     */
    public $sectionModel;
    /**
     * @var integer
     */
    public $reference_section_id;

    /**
     * @var integer
     */
    public $id;
    /**
     * @var string
     */
    public $name;
    /**
     * @var integer
     */
    public $active;

    public function init()
    {
        parent::init();

        $controller = Yii::$app->controller;
        $this->sectionModel = $controller->sectionModel;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['id', 'active', 'reference_section_id'], 'integer'],
            [['name'], 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return $this->sectionModel->attributeLabels();
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = $this->sectionModel::find();

        $dataProvider = new ActiveDataProvider([
            'query'        => $query,
            'reference_id' => $this->sectionModel->reference_id,
        ]);

        $params = [
            $this->formName() => $params,
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id'                   => $this->id,
            'active'               => $this->active,
            'reference_section_id' => $this->reference_section_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }

    /**
     * @return array
     */
    public static function getActiveArray()
    {
        return [
            ''              => 'Любой',
            self::ACTIVE    => 'Активен',
            self::IN_ACTIVE => 'Не активен',
        ];
    }
}