<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 26.03.17
 * Time: 17:05
 */

namespace app\widgets\pager;

use Yii;
use yii\bootstrap\ButtonDropdown;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

class ModernLinkPager extends LinkPager
{
    public function init()
    {
        parent::init();

        $count = Yii::$app->request->getQueryParam('page-size', 20);
        Yii::$app->session->set('page-size', $count);
    }

    public function run()
    {
        echo Html::beginTag('div', ['class' => 'clearfix', 'style' => 'position:relative;']);
        parent::run();

        echo $this->renderPageSizer();

        echo Html::endTag('div');
    }

    public function renderPageSizer()
    {
        $items = [];

        foreach ([20, 50, 100, 250, 500] as $count) {
            $items[] = [
                'label' => $count,
                'url'   => Url::current([
                    'page-size' => $count,
                ]),
            ];
        }
        return ButtonDropdown::widget([
            'containerOptions' => [
                'class' => 'pull-left page-sizer',
            ],
            'options'          => [
                'class' => 'btn-default',
            ],
            'label'            => 'Показывать по: ' . Yii::$app->session->get('page-size') . ' ',
            'dropdown'         => [
                'items' => $items,
            ],
        ]);
    }
}