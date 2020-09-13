<?php


namespace admin\models\forms;


use app\rbac\RbacEnum;

class LoginForm extends \app\models\LoginForm
{

    public function getUser()
    {
        $user = parent::getUser();

        return $user->group != RbacEnum::GROUP_USER ? $user : null;
    }

}