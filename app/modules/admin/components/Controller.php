<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 05.03.17
 * Time: 15:03
 */

namespace app\modules\admin\components;

use Yii;

class Controller extends \yii\web\Controller
{
    public function init()
    {
        parent::init();

        $this->layout = Yii::$app->getModule('admin')->controllerLayout;
    }

    public function beforeAction($action)
    {
        if(!parent::beforeAction($action))
        {
            return false;
        }


        if(Yii::$app->user->isGuest)
        {
            Yii::$app->user->setReturnUrl(Yii::$app->request->url);
            Yii::$app->getResponse()->redirect(['/admin/sign/in'])->send();
            return false;
        }
        else
        {
            if(!Yii::$app->user->can('permAdminPanel')){
                throw new \yii\web\ForbiddenHttpException('You cannot access this action');
            }

            return true;
        }
    }
}