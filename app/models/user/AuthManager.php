<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 21.03.17
 * Time: 19:17
 */

namespace app\models\user;

use Yii;
use yii\rbac\Assignment;
use yii\rbac\PhpManager;

/**
 * Class AuthManager
 * @package app\models\user
 */
class AuthManager extends PhpManager
{
    /**
     * @param int|string $userId
     * @return array
     */
    public function getAssignments($userId)
    {
        if ($userId && $user = $this->getUser($userId)) {
            $assignment = new Assignment();
            $assignment->userId = $userId;
            $assignment->roleName = $user->role;
            return [$assignment->roleName => $assignment];
        }
        return [];
    }

    /**
     * @param string $roleName
     * @param int|string $userId
     * @return null|Assignment
     */
    public function getAssignment($roleName, $userId)
    {
        if ($userId && $user = $this->getUser($userId)) {
            if ($user->role == $roleName) {
                $assignment = new Assignment();
                $assignment->userId = $userId;
                $assignment->roleName = $user->role;
                return $assignment;
            }
        }
        return null;
    }

    /**
     * @param string $roleName
     * @return array
     */
    public function getUserIdsByRole($roleName)
    {
        return User::find()->where(['role' => $roleName])->select('id')->column();
    }

    /**
     * @param \yii\rbac\Role $role
     * @param int|string $userId
     * @return null|Assignment
     */
    public function assign($role, $userId)
    {
        if ($userId && $user = $this->getUser($userId)) {
            $assignment = new Assignment([
                'userId'    => $userId,
                'roleName'  => $role->name,
                'createdAt' => time(),
            ]);
            $this->setRole($user, $role->name);
            return $assignment;
        }
        return null;
    }

    /**
     * @param \yii\rbac\Role $role
     * @param int|string $userId
     * @return bool
     */
    public function revoke($role, $userId)
    {
        if ($userId && $user = $this->getUser($userId)) {
            if ($user->role == $role->name) {
                $this->setRole($user, null);
                return true;
            }
        }
        return false;
    }

    /**
     * @param mixed $userId
     * @return bool
     */
    public function revokeAll($userId)
    {
        if ($userId && $user = $this->getUser($userId)) {
            $this->setRole($user, null);
            return true;
        }
        return false;
    }

    /**
     * @param integer $userId
     * @return null|\yii\web\IdentityInterface|User
     */
    private function getUser($userId)
    {
        $webUser = Yii::$app->get('user', false);
        if ($webUser && !$webUser->getIsGuest() && $webUser->getId() == $userId) {
            return $webUser->getIdentity();
        } else {
            return User::findOne($userId);
        }
    }

    /**
     * @param User $user
     * @param $roleName
     */
    private function setRole(User $user, $roleName)
    {
        $user->role = $roleName;
        $user->updateAttributes(['role' => $roleName]);
    }
}