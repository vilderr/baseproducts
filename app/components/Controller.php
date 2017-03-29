<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 21.03.17
 * Time: 12:51
 */

namespace app\components;

use Yii;
use yii\filters\AccessControl;

/**
 * Class Controller
 * @package app\components
 */
class Controller extends \yii\web\Controller
{
    /**
     * collect controller errors
     * @var array
     */
    public $errors = [];

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [Rbac::PERMISSION_ADMIN_PANEL],
                    ],
                ],
            ],
        ];
    }

    /**
     * initialize controller data
     */
    public function init()
    {
        parent::init();
        $this->layout = 'main';
    }

    /**
     * @param string $type
     * @param string|array $mess
     */
    public function flash($type, $mess)
    {
        if (is_string($mess)) {
            $mess = [$mess];
        }

        Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, implode('<br>', $mess));
    }
}