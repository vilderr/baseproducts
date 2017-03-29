<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 05.03.17
 * Time: 16:42
 */

namespace app\commands;

use yii\base\Model;
use yii\console\Controller;
use app\modules\user\models\User;
use yii\console\Exception;
use yii\helpers\Console;

class UserController extends Controller
{
    /**
     * Creates new user
     */
    public function actionCreate()
    {
        $user = new User();
        $this->readValue($user, 'username');
        $this->readValue($user, 'name');
        $this->readValue($user, 'last_name');
        $this->readValue($user, 'email');
        $user->setPassword($this->prompt('Password:', [
            'required' => true,
            'pattern' => '#^.{6,255}$#i',
            'error' => 'More than 6 symbols',
        ]));
        $user->generateAuthKey();
        $this->log($user->save());
    }
    /**
     * Removes user by username
     */
    public function actionDelete()
    {
        $username = $this->prompt('Username:', ['required' => true]);
        $user = $this->findModel($username);
        $this->log($user->delete());
    }
    /**
     * Activates user
     */
    public function actionActivate()
    {
        $username = $this->prompt('Username:', ['required' => true]);
        $user = $this->findModel($username);
        $user->status = User::STATUS_ACTIVE;
        $user->removeEmailConfirmToken();
        $this->log($user->save());
    }
    /**
     * Changes user password
     */
    public function actionChangePassword()
    {
        $username = $this->prompt('Username:', ['required' => true]);
        $user = $this->findModel($username);
        $user->setPassword($this->prompt('New password:', [
            'required' => true,
            'pattern' => '#^.{6,255}$#i',
            'error' => 'More than 6 symbols',
        ]));
        $this->log($user->save());
    }
    /**
     * @param string $username
     * @throws \yii\console\Exception
     * @return User the loaded model
     */
    private function findModel($username)
    {
        if (!$user = User::findOne(['username' => $username])) {
            throw new Exception('User not found');
        }
        return $user;
    }
    /**
     * @param Model $user
     * @param string $attribute
     */
    private function readValue($user, $attribute)
    {
        $user->$attribute = $this->prompt(mb_convert_case($attribute, MB_CASE_TITLE, 'utf-8') . ':', [
            'validator' => function ($input, &$error) use ($user, $attribute) {
                $user->$attribute = $input;
                if ($user->validate([$attribute])) {
                    return true;
                } else {
                    $error = implode(',', $user->getErrors($attribute));
                    return false;
                }
            },
        ]);
    }
    /**
     * @param bool $success
     */
    private function log($success)
    {
        if ($success) {
            $this->stdout('Success!', Console::FG_GREEN, Console::BOLD);
        } else {
            $this->stderr('Error!', Console::FG_RED, Console::BOLD);
        }
        $this->stdout(PHP_EOL);
    }
}