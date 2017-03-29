<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 10.03.17
 * Time: 11:36
 */

namespace app\modules\admin\behaviors;

use creocoder\nestedsets\NestedSetsBehavior;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class CacheBehavior extends Behavior
{
    /**
     * @var array
     */
    public $cache_id;

    /**
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT  => 'deleteCache',
            ActiveRecord::EVENT_AFTER_UPDATE  => 'deleteCache',
            ActiveRecord::EVENT_AFTER_DELETE  => 'deleteCache',
        ];
    }

    public function deleteCache()
    {
        foreach ($this->cache_id as $id) {
            Yii::$app->cache->delete($id);
        }
    }
}