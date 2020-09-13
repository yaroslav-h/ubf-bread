<?php


namespace res\forms;


use Yii;
use yii\base\Model;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\web\ServerErrorHttpException;
use app\components\helpers\RecaptchaHelper;

class LoginForm extends Model
{
    const SCENARIO_LOGIN = 'login';
    const SCENARIO_GOOGLE = 'google';

    public $provider;
    public $code;
    public $token;

    public $username;
    public $password;

    private $_google_id = false;
    private $_user = false;

    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['provider', 'username', 'password', 'token', 'code'],
            self::SCENARIO_LOGIN => ['username', 'password', 'token'],
            self::SCENARIO_GOOGLE => ['provider', 'code'],
        ];
    }

    public function rules()
    {
        return [
            [['username', 'password'], 'trim'],
            [['username', 'password'], 'required'],
            [['username', 'password'], 'string'],

            [['provider', 'code'], 'trim'],
            [['provider', 'code'], 'required'],
            [['provider', 'code'], 'string'],
        ];
    }

    public function login()
    {
        if($this->provider == 'google') {
            $this->scenario = self::SCENARIO_GOOGLE;
        } else {
            $this->scenario = self::SCENARIO_LOGIN;
        }

        if($this->validate()) {
            if($this->scenario == self::SCENARIO_LOGIN) {
                if(!RecaptchaHelper::isVerified($this->token)) {
                    $this->addError('username', 'Are you a real person?');
                    return false;
                }

                if($this->getUser() == null) {
                    $this->addError('password', 'Username or password is incorrect.');
                    return false;
                }

                if(!Yii::$app->security->validatePassword($this->password, $this->getUser()->password_hash)) {
                    $this->addError('password', 'Username or password is incorrect.');
                    return false;
                }
            } elseif($this->scenario == self::SCENARIO_GOOGLE) {
                if($this->getUser() == null) {
                    $this->addError('code', 'Unable to find any user attached to the google account. Try to signup first.');
                    return false;
                }
            } else {
                throw new ServerErrorHttpException('Unable to detect scenario!');
            }

            return Yii::$app->user->login($this->getUser(), 180*24*3600);
        }

        return false;
    }

    public function getUser()
    {
        if($this->_user === false) {
            if($this->scenario == self::SCENARIO_LOGIN) {
                $this->_user = User::findByUsername($this->username);
            } elseif($this->scenario == self::SCENARIO_GOOGLE) {
                $this->_user = User::findByGoogleId($this->getGoogleId());
            }
        }

        return $this->_user;
    }


    public function getGoogleId()
    {
        if($this->_google_id === false) {
            if(!$this->getClient()->fetchAccessToken($this->code)) {
                throw new ServerErrorHttpException('Unable to get access token.');
            }

            $attributes = $this->getClient()->getUserAttributes();

            if(ArrayHelper::getValue($attributes, 'id')) {
                $this->_google_id = ArrayHelper::getValue($attributes, 'id');
            } else {
                throw new ServerErrorHttpException('Unable to get user ID from google service.');
            }
        }

        return $this->_google_id;
    }

    /**
     *  @return \yii\authclient\ClientInterface|\yii\authclient\clients\Google
     */
    protected function getClient()
    {
        return Yii::$app->auth->getClient('google');
    }
}
