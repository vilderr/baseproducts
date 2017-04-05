<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 05.04.17
 * Time: 0:35
 */

namespace app\models\distribution;

use app\models\reference\ReferenceElement;

final class Parser
{
    /**
     * @var array
     */
    public $next_step = [];
    /**
     * @var \app\models\distribution\Distribution
     */
    public $distribution;

    /**
     * @param $NS
     */
    public function init(&$NS)
    {
        $this->next_step = &$NS;
        $this->distribution = Distribution::find()->limit(1)->where(['id' => $this->next_step['id']])->with(['reference', 'activeParts'])->one();
        if (!isset($this->next_step['last_id'])) {
            $this->next_step['last_id'] = 0;
        }

        if (!isset($this->next_step['all_counter'])) {
            $this->next_step['all_counter'] = 0;
        }
    }

    /**
     * @param $part
     * @param $start_time
     * @param int $interval
     * @return int
     */
    public function parseElements($part, $start_time, $interval = 30)
    {
        $counter = 0;

        $data = unserialize($this->distribution->activeParts[$part]->data);

        $query = ReferenceElement::find()
            ->limit(2000)
            ->where(['>', 'id', $this->next_step['last_id']])
            ->andWhere(['reference_id' => $this->distribution->reference->id]);

        foreach ($data['condition'] as $name => $value) {
            switch ($name) {
                case 'name':
                case 'props':
                    $query->andFilterWhere(['or like', $name, explode(' ', $value)]);
                    break;
                case 'section':
                    $query->andFilterWhere(['reference_section_id' => $value]);
                    break;
                case 'price':
                    $query->andFilterWhere(['>', 'price', $value['from']])->andFilterWhere(['<', 'price', $value['to']]);
                    break;
            }
        }

        foreach ($query->all() as $element) {
            $result = $this->updateElement($element, $data['operation']);
            $this->next_step['last_id'] = $element->id;
            $counter++;

            if ($interval > 0 && (time() - $start_time) > $interval)
                break;
        }

        $this->next_step['all_counter'] += $counter;

        return $counter;
    }

    /**
     * @param ReferenceElement $element
     * @param $operations
     * @return false|int
     */
    protected function updateElement(ReferenceElement $element, $operations)
    {
        foreach ($operations as $name => $value) {
            switch ($name) {
                case 'active':
                    $element->active = $value;
                    break;
                case 'section':
                    $element->reference_section_id = $value;
                    break;
            }
        }

        return $element->update();
    }
}