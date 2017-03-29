<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 05.03.17
 * Time: 15:32
 */

namespace app\modules\admin\controllers;

use Yii;
use app\modules\user\models\forms\LoginForm;
use yii\web\Controller;

class SignController extends Controller
{
    public $layout = 'sign';

    public function actionIn()
    {
        $model = new LoginForm;

        if (!Yii::$app->user->isGuest || ($model->load(Yii::$app->request->post()) && $model->login()))
        {
            return $this->redirect(['/admin']);
        }
        else
        {
            return $this->render('in', ['model' => $model]);
        }
    }

    public function actionOut()
    {
        Yii::$app->user->logout();

        return $this->redirect(['/admin']);
    }
}