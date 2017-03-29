<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 12.03.17
 * Time: 15:39
 */

namespace app\modules\admin\models\search;

use Yii;
use yii\base\Model;
use app\modules\admin\components\ActiveDataProvider;

/**
 * Class ReferenceElementSearch
 * @package app\modules\admin\models\search
 */
class ReferenceElementSearch extends Model
{
    const IN_ACTIVE = 0;
    const ACTIVE = 1;

    /**
     * @var \app\modules\admin\models\ReferenceElement
     */
    public $elementModel;
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
    /**
     * @var integer
     */
    public $sort;
    /**
     * @var bool
     * Если данное свойство установлено в true, в методе "search" ищем все элементы включая подразделы
     */
    public $include_subsections = false;


    public function init()
    {
        parent::init();

        $controller = Yii::$app->controller;

        $this->sectionModel = $controller->sectionModel;
        $this->elementModel = $controller->elementModel;
    }

    public function rules()
    {
        return [
            [['id', 'active', 'sort'], 'integer'],
            [['include_subsections'], 'boolean'],
            [['name'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return $this->elementModel->attributeLabels();
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = $this->elementModel::find();

        $dataProvider = new ActiveDataProvider([
            'query'        => $query,
            'reference_id' => $this->elementModel->reference_id,
        ]);

        $params = [
            $this->formName() => $params,
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id'     => $this->id,
            'active' => $this->active,
            'sort'   => $this->sort,
        ]);

        if ($this->include_subsections) {
            if (($section = $this->sectionModel::findOne($this->reference_section_id)) === null) {
                $section = $this->sectionModel;
            }

            $sect = $section->getChildrenIDs();
        } else {
            $sect = $this->reference_section_id;
        }

        $query->andFilterWhere([
            'reference_section_id' => $sect,
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