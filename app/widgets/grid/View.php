<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 01.04.17
 * Time: 23:47
 */

namespace app\widgets\grid;


use yii\helpers\Html;
use yii\grid\GridView;

class View extends GridView
{
    public $actions = [];

    /**
     * @deprecated of parent
     * Renders the table footer.
     * @return string the rendering result.
     */
    public function renderTableFooter()
    {
        $content = Html::tag('tr', Html::tag('td', $this->renderGroupAction(), ['colspan' => count($this->columns)]), $this->footerRowOptions);
        if ($this->filterPosition === self::FILTER_POS_FOOTER) {
            $content .= $this->renderFilters();
        }

        return "<tfoot>\n" . $content . "\n</tfoot>";
    }

    public function renderGroupAction()
    {
        $html = '';
        $html .= Html::beginForm();
        $html .= Html::beginTag('div', ['class' => 'form-inline']);
        $html .= $this->renderActionDropdown();
        $html .= Html::tag('div', '', ['class' => 'form-group', 'id' => 'action-box'])."\n";
        $html .= Html::button('Применить', ['type' => 'submit', 'name' => 'group-action', 'value' => 'Y', 'class' => 'btn btn-primary', 'disabled' => true]);
        $html .= Html::endForm();

        return $html;
    }

    public function renderActionDropdown()
    {
        $html = '';
        $html .= Html::beginTag('div', ['class' => 'form-group']);
        $html .= Html::tag('label', 'С выбранными: ') . "\n";
        $html .= Html::dropDownList('action', 'DEA', $this->actions, ['class' => 'form-control']);
        $html .= Html::endTag('div') . "\n";

        return $html;
    }
}