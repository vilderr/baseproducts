<?php

namespace app\modules\admin;

use Yii;
use yii\base\BootstrapInterface;
use yii\filters\AccessControl;
use app\modules\admin\components\Rbac;

/**
 * admin module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\admin\controllers';

    public $controllerLayout = '@app/modules/admin/views/layouts/main';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        // custom initialization code goes here
    }
}
