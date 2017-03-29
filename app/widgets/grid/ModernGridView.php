<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 26.03.17
 * Time: 16:50
 */

namespace app\widgets\grid;


use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\LinkPager;

class ModernGridView extends GridView
{
    public function renderPager()
    {
        $pagination = $this->dataProvider->getPagination();
        if ($pagination === false || $this->dataProvider->getCount() <= 0) {
            return '';
        }
        /* @var $class LinkPager */
        $pager = $this->pager;
        $class = ArrayHelper::remove($pager, 'class', LinkPager::className());
        $pager['pagination'] = $pagination;
        $pager['view'] = $this->getView();

        return $class::widget($pager);
    }
}