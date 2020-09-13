<?php


namespace admin\models;


use app\rbac\RbacEnum;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\HtmlPurifier;

class User extends \app\models\User
{

    public $password;

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ]
        ];
    }

    public function rules()
    {
        return [
            [['name', 'email'], 'required'],
            [['name', 'email', 'password'], 'trim', 'skipOnEmpty' => true],
            [['name', 'email'], function($attr) {
                $this->$attr = HtmlPurifier::process($this->$attr);
            }],
            [['password'], 'required', 'when' => function() { return $this->isNewRecord; }],
            [['name', 'email'], 'string', 'max' => 255],
            [['password'], 'string', 'min' => 5, 'max' => 20],
            ['email', 'email'],
            ['email', 'unique'],
        ];
    }

    public function setNewPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
        $this->auth_key = Yii::$app->security->generateRandomString();
        // TODO: invalidate all user`s sessions...
    }

    public function beforeSave($insert)
    {
        if(!parent::beforeSave($insert)) {
            return false;
        }

        if($insert) {
            $this->auth_key = Yii::$app->security->generateRandomString();
        }

        if($this->password) {
           $this->setNewPassword($this->password);
        }

        return true;
    }

    public function getGroup_name()
    {
        return RbacEnum::getGroupName($this->group);
    }
}