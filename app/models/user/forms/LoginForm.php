<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 21.03.17
 * Time: 13:19
 */

namespace app\models\user\forms;

use Yii;
use yii\base\Model;
use app\models\user\User;

/**
 * Class LoginForm
 * @package app\models\user\forms
 */
class LoginForm extends Model
{
    /**
     * @var string
     */
    public $username;
    /**
     * @var string
     */
    public $password;
    /**
     * @var bool
     */
    public $rememberMe = true;
    /**
     * @var bool|\app\models\user\User
     */
    private $_user = false;

    /**
     * rules for user validation
     * @return array
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'username'   => Yii::t('app/user', 'login'),
            'password'   => Yii::t('app/user', 'password'),
            'rememberMe' => Yii::t('app/user', 'remember-me'),
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError('password', Yii::t('app/user', 'wrong-login-or-password'));
            } elseif ($user && $user->status == User::STATUS_BLOCKED) {
                $this->addError('username', Yii::t('app/user', 'account-blocked'));
            } elseif ($user && $user->status == User::STATUS_WAIT) {
                $this->addError('username', Yii::t('app/user', 'account-not-approved'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }

    /**
     * Finds user by [[login]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}