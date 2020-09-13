<?php

namespace app\models\forms;


use admin\models\User;
use Yii;
use yii\base\Model;

class ChangePasswordForm extends Model
{

    public $current_password;
    public $new_password;
    public $repeat_new_password;

    public function rules()
    {
        return [
            [['current_password', 'new_password', 'repeat_new_password'], 'trim'],
            [['current_password', 'new_password', 'repeat_new_password'], 'required'],
            [['current_password', 'new_password', 'repeat_new_password'], 'string', 'min' => 6, 'max' => 20],
            ['current_password', 'validatePassword'],
            ['repeat_new_password', 'compare', 'compareAttribute' => 'new_password'],
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if(!Yii::$app->security->validatePassword($this->$attribute, $this->getUser()->password_hash)) {
            $this->addError($attribute, Yii::t('app', 'Incorrect current password.'));
        }
    }

    public function save()
    {
        if($this->validate()) {

            $this->getUser()->setNewPassword($this->new_password);

            if($this->getUser()->save(false, ['password_hash', 'auth_key'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return User|null
     */
    public function getUser()
    {
        return User::findOne(['id' => getMyId()]);
    }
}
