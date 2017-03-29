<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 21.03.17
 * Time: 13:32
 */

namespace app\controllers;

use Yii;
use app\models\user\forms\LoginForm;
use yii\helpers\Url;
use yii\web\Controller;

/**
 * Class SignController
 * @package app\controllers
 */
class SignController extends Controller
{
    public $layout = 'sign';

    /**
     * @return string|\yii\web\Response
     */
    public function actionIn()
    {
        $model = new LoginForm;

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if ($model->login()) {
                return $this->redirect('/');
            }
        }

        return $this->render('in', [
            'model' => $model,
        ]);
    }

    /**
     * logout user method
     */
    public function actionOut()
    {
        Yii::$app->user->logout();

        $this->redirect('/');
    }
}