<?php


namespace app\rbac;

use yii\rbac\Rule;

class UserGroupRule extends Rule
{
    public $name = 'userGroup';

    public function execute($user, $item, $params)
    {
        if (isLoggedIn()) {
            $group = getMyIdentity()->group;


            if ($item->name === RbacEnum::ADMIN) {
                return $group == RbacEnum::GROUP_ADMIN;
            } elseif ($item->name === RbacEnum::MODER) {
                return $group == RbacEnum::GROUP_ADMIN || $group == RbacEnum::GROUP_MODER;
            } elseif ($item->name === RbacEnum::USER) {
                return $group == RbacEnum::GROUP_ADMIN || $group == RbacEnum::GROUP_MODER || $group == RbacEnum::GROUP_USER;
            }
        }

        return false;
    }
}