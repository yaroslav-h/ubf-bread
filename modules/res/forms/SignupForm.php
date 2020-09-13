<?php


namespace res\forms;


use app\components\RecaptchaHelper;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\validators\EmailValidator;
use yii\validators\StringValidator;
use yii\web\ServerErrorHttpException;
use app\components\validators\UsernameValidator;
use api\Module;
use api\models\User;
use jobs\search\SearchUpdateIndexJob;

class SignupForm extends Model
{

    const SCENARIO_EMAIL = 'email';
    const SCENARIO_GOOGLE = 'google';

    public $provider;
    public $username;
    public $email;
    public $password;
    public $code;
    public $token;

    private $_google;

    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['provider', 'username', 'email', 'password', 'code', 'token'],
            self::SCENARIO_EMAIL => ['username', 'email', 'password', 'code', 'token'],
            self::SCENARIO_GOOGLE => ['provider', 'code'],
        ];
    }

    public function rules()
    {
        return [
            [['provider', 'username', 'email', 'password', 'code'], 'trim'],
            [['provider', 'username', 'email', 'password', 'code'], 'required'],
            [['provider', 'username', 'email', 'password', 'code'], 'string'],
            ['username', 'validateUsername'],
            ['email',    'validateEmail'],
            ['password', 'validatePassword'],
            ['code', 'validateCode'],

            [['token'], 'required'],
        ];
    }

    public function request()
    {
        if($this->validate(['username', 'email', 'password'])) {

            if(!RecaptchaHelper::isVerified($this->token)) {
                $this->addError('username', 'Are you a real person?');
                return false;
            }

            if(Module::sendEmailConfirmationCode('signup', $this->email)) {
                return true;
            }

            $this->addError('error', 'Something went wrong. Please try again');

            return false;
        }

        return false;
    }

    public function create()
    {
        if($this->validate()) {

            $user = new User([
                'username'  => $this->username,
                'name'      => '',
                'email'     => $this->email,
                'password'  => Yii::$app->security->generatePasswordHash($this->password),
                'is_private' => 0,
                'created_at' => time(),
            ]);

            $user->generateAuthKey();

            if(!$user->save(false)) {
                throw new ServerErrorHttpException('Unable to save user.');
            }

            if($user->mapIdBy('username') && $user->mapIdBy('email')) {

                Yii::$app->queues->push(new SearchUpdateIndexJob([
                    'type' => SearchUpdateIndexJob::TYPE_USERS,
                    'id'   => $user->id,
                ]));

                return Yii::$app->user->login($user, 90*24*3600);
            }

        }

        return false;
    }

    public function createByGoogle()
    {
        if($this->validate()) {

            $user = new User([
                'username'  => $this->createUsername($this->getGoogle('email')),
                'name'      => $this->getGoogle('name'),
                'email'     => $this->getGoogle('email'),
                'google_id' => $this->getGoogle('id'),
                'is_private' => 0,
                'created_at' => time(),
            ]);

            $user->generateAuthKey();

            if(!$user->save(false)) {
                throw new ServerErrorHttpException('Unable to save user.');
            }

            if($user->mapIdBy('username') && $user->mapIdBy('email') && $user->mapIdBy('google_id')) {

                Yii::$app->queues->push(new SearchUpdateIndexJob([
                    'type' => SearchUpdateIndexJob::TYPE_USERS,
                    'id'   => $user->id,
                ]));

                return Yii::$app->user->login($user, 90*24*3600);
            } else {
                // TODO: FIX THIS AFTER
            }

        }

        return false;
    }

    public function signup()
    {
        if($this->provider == 'google') {
            $this->scenario = self::SCENARIO_GOOGLE;
        } else {
            $this->scenario = self::SCENARIO_EMAIL;
        }

        if($this->scenario == self::SCENARIO_EMAIL) {
            if($this->code == 'request') {
                return $this->request();
            }

            return $this->create();
        } elseif($this->scenario == self::SCENARIO_GOOGLE) {
            return $this->createByGoogle();
        }

        return false;
    }

    public function validateUsername($attribute)
    {
        $validator = new UsernameValidator();
        $error = null;

        if($validator->validate($this->{$attribute}, $error) === false) {
            $this->addError($attribute, $error);
        }

        if($error === null) {
            if(User::getMappedId($attribute, $this->{$attribute})) {
                $this->addError($attribute, 'This '.$attribute.' is already taken.');
            }
        }
    }

    public function validateEmail($attribute)
    {
        $validator = new EmailValidator();
        $error = null;

        if($validator->validate($this->{$attribute}, $error) === false) {
            $this->addError($attribute, $error);
        }

        if($error === null) {
            if(User::getMappedId($attribute, $this->{$attribute})) {
                $this->addError($attribute, 'This '.$attribute.' is already taken.');
            }
        }
    }

    public function validatePassword($attribute)
    {
        $validator = new StringValidator(['min' => 6, 'max' => 24]);
        $error = null;

        if($validator->validate($this->{$attribute}, $error) === false) {
            $this->addError($attribute, $error);
        }
    }

    public function validateCode($attribute)
    {
        if($this->scenario == self::SCENARIO_EMAIL) {
            if(!Module::checkEmailConfirmationCode('signup', $this->email, $this->{$attribute})) {
                $this->addError($attribute, 'This is the wrong code.');
            }
        } elseif($this->scenario == self::SCENARIO_GOOGLE) {
            if(!$this->hasErrors()) {
                if(User::getMappedId('google_id', $this->getGoogle('id'))) {
                    $this->addError('code', 'This google account is already used.');
                }
            }

            if(!$this->hasErrors()) {
                if(User::getMappedId('email', $this->getGoogle('email'))) {
                    $this->addError('code', 'This google account`s email is already used.');
                }
            }
        }
    }




    public function getGoogle(string $key = null)
    {
        if($this->_google === null) {

            if(!$this->getClient()->fetchAccessToken($this->code)) {
                throw new ServerErrorHttpException('Unable to get access token.');
            }

            $attributes = $this->getClient()->getUserAttributes();

            if(ArrayHelper::getValue($attributes, 'id')) {
                $this->_google = [
                    'id' => ArrayHelper::getValue($attributes, 'id'),
                    'name' => ArrayHelper::getValue($attributes, 'name'),
                    'email' => ArrayHelper::getValue($attributes, 'email'),
                ];
            } else {
                throw new ServerErrorHttpException('Unable to get user ID from google service.');
            }
        }

        if($key) {
            return $this->_google[$key] ?? null;
        }

        return $this->_google;
    }

    /**
     *  @return \yii\authclient\ClientInterface|\yii\authclient\clients\Google
     */
    protected function getClient()
    {
        return Yii::$app->auth->getClient('google');
    }

    public function createUsername($email)
    {
        if(empty($email)) {
            throw new ServerErrorHttpException;
        }

        $items = explode('@', $email);

        $username = $items[0];

        $counter = 0;

        while(User::getMappedId('username', $username)) {
            $username = $username . rand(100, 999);
            $counter++;

            if($counter > 3) {
                throw new ServerErrorHttpException;
            }
        }

        return $username;
    }
}
