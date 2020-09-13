<?php


namespace res\forms;


use api\models\User;
use app\components\RecaptchaHelper;
use Yii;
use yii\base\Model;
use api\Module;

class RecoverPasswordForm extends Model
{

    public $step;
    public $email;
    public $code;
    public $password;
    public $token;

    public function rules()
    {
        return [
            ['token', 'required'],
            ['step', 'required'],
            ['step', 'in', 'range' => ['email', 'code', 'password']],
            ['email', 'required'],
            ['email', 'email', 'when' => function() {
                return Yii::$app->user->isGuest;
            }],
            [['code'], 'required', 'when' => function() {
                return $this->step == 'code' || $this->step == 'password';
            }],
            ['password', 'required', 'when' => function() {
                return $this->step == 'password';
            }],
            [['password'], 'string', 'min' => 6, 'max' => 20, 'when' => function() {
                return $this->step == 'password';
            }],
        ];
    }

    public function save()
    {
        if($this->validate()) {


            if(!Yii::$app->user->isGuest) {
                $this->email = Yii::$app->user->identity->email;
            }

            if($this->step == 'email') {
                if(User::getMappedId('email', $this->email)) {
                    if(!Module::sendEmailConfirmationCode('recover', $this->email)) {
                        $this->addError('email', 'Unable to send a code.');
                    }
                } else {
                    $this->addError('email', 'Invalid email');
                }
            } elseif($this->step == 'code') {
                if(User::getMappedId('email', $this->email)) {
                    if(!Module::checkEmailConfirmationCode('recover', $this->email, $this->code)) {
                        $this->addError('code', 'This is the wrong code.');
                    }
                } else {
                    $this->addError('code', 'This is the wrong code.');
                }
            } elseif($this->step == 'password') {
                if(User::getMappedId('email', $this->email)) {
                    if(!Module::checkEmailConfirmationCode('recover', $this->email, $this->code)) {
                        $this->addError('password', 'Something went wrong. Please try again.');
                    }
                } else {
                    $this->addError('password', 'Something went wrong. Please try again.');
                }
            }

            if(!RecaptchaHelper::isVerified($this->token)) {
                $this->addError($this->step, 'Are you a real person?');
                return false;
            }

            if($this->hasErrors()) {
                return false;
            }

            if($this->step == 'email') {
                $this->step = 'code';
            } elseif($this->step == 'code') {
                $this->step = 'password';
            } elseif($this->step == 'password') {

                $userId = User::getMappedId('email', $this->email);
                $result = false;

                if($userId) {
                    $user = User::get($userId);
                    $user->setDataField('password', Yii::$app->security->generatePasswordHash($this->password));
                    $result = $user->save();
                }

                if($result) {
                    $this->step = 'done';
                } else {
                    $this->addError('password', 'Something went wrong. Please try again.');
                }
            }

            return !$this->hasErrors();
        }

        return false;
    }

}
