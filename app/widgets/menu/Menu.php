<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 08.03.17
 * Time: 11:32
 */

namespace app\widgets\menu;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\widgets\menu\assets\MenuAsset;

/**
 * Class AdminMenuWidget
 * @package app\modules\admin\widgets\menu
 */
class Menu extends Widget
{
    public $options = [];

    public $items = [];

    /**
     * @var boolean whether the nav items labels should be HTML-encoded.
     */
    public $encodeLabels = true;
    /**
     * @var boolean whether to automatically activate items according to whether their route setting
     * matches the currently requested route.
     * @see isItemActive
     */
    public $activateItems = true;
    /**
     * @var boolean whether to activate parent menu items when one of the corresponding child menu items is active.
     */
    public $activateParents = true;

    public $route;

    public $params;

    public $dropDownCaret;

    public $direction;

    public function init()
    {
        parent::init();

        if ($this->route === null && Yii::$app->controller !== null) {
            $this->route = Yii::$app->controller->getRoute();
        }
        if ($this->params === null) {
            $this->params = Yii::$app->request->getQueryParams();
        }
        if ($this->dropDownCaret === null) {
            $this->dropDownCaret = Html::tag('i', '', ['class' => 'fa fa-angle-down']);
        }

        if ($this->direction === null) {
            $this->direction = 'vertical';
        }

        Html::addCssClass($this->options, ['widget' => 'main-nav ' . $this->direction]);
    }

    public function run()
    {
        MenuAsset::register($this->getView());

        return $this->renderItems();
    }

    /**
     * @return string
     */
    public function renderItems()
    {
        $items = [];
        foreach ($this->items as $i => $item) {
            if (isset($item['visisble']) && !$item['visible'])
                continue;

            $items[] = $this->renderItem($item);
        }

        return Html::tag('ul', implode("\n", $items), $this->options);
    }

    public function renderItem($item)
    {
        if (is_string($item))
            return $item;
        if (!isset($item['label'])) {
            throw new InvalidConfigException("The 'label' option is required.");
        }

        $encodeLabel = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
        $label = $encodeLabel ? Html::encode($item['label']) : $item['label'];
        $options = ArrayHelper::getValue($item, 'options', []);
        $url = ArrayHelper::getValue($item, 'url', '#');
        $items = ArrayHelper::getValue($item, 'items');
        $linkOptions = ArrayHelper::getValue($item, 'linkOptions', []);

        if (isset($item['active'])) {
            $active = ArrayHelper::remove($item, 'active', false);
        } else {
            $active = $this->isItemActive($item);
        }

        if (empty($items)) {
            $items = '';
        } else {
            Html::addCssClass($options, ['widget' => 'drop-down']);
            Html::addCssClass($linkOptions, ['widget' => 'drop-down-toggle']);

            if (is_array($items)) {
                if ($this->activateItems) {
                    $items = $this->isChildActive($items, $active);
                }
                $items = $this->renderDropdown($items, $item);
            }
        }

        if ($this->activateItems && $active) {
            Html::addCssClass($options, 'active');
            $this->dropDownCaret = Html::tag('i', '', ['class' => 'fa fa-angle-up']);
        }

        return Html::tag('li', ($items == '' ? '' : $this->dropDownCaret) . Html::a($label, $url, $linkOptions) . $items, $options);
    }

    /**
     * Renders the given items as a dropdown.
     * This method is called to create sub-menus.
     * @param array $items the given items. Please refer to [[Dropdown::items]] for the array structure.
     * @param array $parentItem the parent item information. Please refer to [[items]] for the structure of this array.
     * @return string the rendering result.
     * @since 2.0.1
     */
    protected function renderDropdown($items, $parentItem)
    {
        return Dropdown::widget([
            'options'      => ArrayHelper::getValue($parentItem, 'dropDownOptions', []),
            'items'        => $items,
            'encodeLabels' => $this->encodeLabels,
            'view'         => $this->getView(),
        ]);
    }

    /**
     * Check to see if a child item is active optionally activating the parent.
     * @param array $items @see items
     * @param boolean $active should the parent be active too
     * @return array @see items
     */
    protected function isChildActive($items, &$active)
    {
        foreach ($items as $i => $child) {
            if (ArrayHelper::remove($items[$i], 'active', false) || $this->isItemActive($child)) {
                Html::addCssClass($items[$i]['options'], 'active');
                if ($this->activateParents) {
                    $active = true;
                }
            }
        }

        return $items;
    }

    /**
     * Checks whether a menu item is active.
     * This is done by checking if [[route]] and [[params]] match that specified in the `url` option of the menu item.
     * When the `url` option of a menu item is specified in terms of an array, its first element is treated
     * as the route for the item and the rest of the elements are the associated parameters.
     * Only when its route and parameters match [[route]] and [[params]], respectively, will a menu item
     * be considered active.
     * @param array $item the menu item to be checked
     * @return boolean whether the menu item is active
     */
    protected function isItemActive($item)
    {
        if (isset($item['url']) && is_array($item['url']) && isset($item['url'][0])) {

            $route = $item['url'][0];

            if ($route[0] !== '/' && Yii::$app->controller) {
                $route = Yii::$app->controller->module->getUniqueId() . '/' . $route;
            }

            if (ltrim($route, '/') !== $this->route) {
                return false;
            }
            unset($item['url']['#']);

            if (count($item['url']) > 1) {
                $params = $item['url'];
                unset($params[0]);
                foreach ($params as $name => $value) {
                    if ($value !== null && (!isset($this->params[$name]) || $this->params[$name] != $value)) {
                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }
}