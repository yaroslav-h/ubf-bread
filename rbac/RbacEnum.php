<?php


namespace app\rbac;


class RbacEnum
{

    const GROUP_ADMIN = 1;
    const GROUP_MODER = 20;
    const GROUP_USER = 30;

    const ADMIN = 'admin';
    const MODER = 'moder';
    const USER = 'user';

    const DEFAULT_GROUP = 30;

    public static function groups()
    {
        return [
            self::GROUP_ADMIN => \Yii::t('app', 'Admin'),
            self::GROUP_MODER => \Yii::t('app', 'Moder'),
            self::GROUP_USER => \Yii::t('app', 'User'),
        ];
    }

    public static function getGroupName($group)
    {
        return self::groups()[$group] ?? 'unknown';
    }
}