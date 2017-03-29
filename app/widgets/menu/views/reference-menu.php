<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 08.03.17
 * Time: 11:42
 * @var $this yii\web\View
 * @var $items array
 */
?>
<ul id="references" class="menu">
<?
foreach ($items as $key => $item)
{
    if(!empty($item))
    {
        ?>
        <li class="root-item">
            <a href="javascript:void(0);" class="menu-item"><i class="icon fa fa-angle-down"></i><?=$key;?></a>
            <ul class="child-items">
                <?foreach ($item as $oneItem):?>
                <li>
                    <a href="" class="menu-item"><?=$oneItem->name?></a>
                </li>
                <?endforeach;?>
            </ul>
        </li>
        <?
    }
}
?>
</ul>

