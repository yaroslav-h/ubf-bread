<?php

namespace app\components;


use Yii;
use yii\web\ForbiddenHttpException;

/**
 * @property \app\models\User $identity
 **/

class User extends \yii\web\User
{

    public $identityClass = \app\models\User::class;

    public $enableAutoLogin = true;

    public $loginUrl = null;

    protected $_beforeLoginSessionId;
    protected $_beforeLogoutSessionId;

    public function init()
    {
        parent::init();
    }

    public function beforeLogin($identity, $cookieBased, $duration)
    {
        if(!parent::beforeLogin($identity, $cookieBased, $duration)) {
            return false;
        }

        $this->_beforeLoginSessionId = Yii::$app->session->id;

        return true;
    }

    public function afterLogin($identity, $cookieBased, $duration)
    {
        parent::afterLogin($identity, $cookieBased, $duration);

        /*Yii::$app->queues->push(new UserLoginJob([
            'user_id'     => $identity->getId(),
            'old_session_id' => $this->_beforeLoginSessionId,
            'new_session_id' => Yii::$app->session->id,
            'ip'         => Yii::$app->request->userIP,
            'agent'      => Yii::$app->request->userAgent,
            'use_cookie' => $cookieBased,
            'duration'   => $duration,
        ]));*/
    }

    public function beforeLogout($identity)
    {
        if(!parent::beforeLogout($identity)) {
            return false;
        }

        $this->_beforeLogoutSessionId = Yii::$app->session->id;

        return true;
    }

    public function afterLogout($identity)
    {
        parent::afterLogout($identity);

        /*if($this->_beforeLogoutSessionId) {
            Login::removeBySessionId($identity->getId(), $this->_beforeLogoutSessionId);
        }*/
    }

    protected function renewAuthStatus()
    {
        parent::renewAuthStatus();
    }

    /**
     * @inheritdoc
     */
    public function loginRequired($checkAjax = true, $checkAcceptHeader = true)
    {
        if(Yii::$app->controller->module->id === 'admin') {
            parent::loginRequired($checkAjax, $checkAcceptHeader);
        } else {
            throw new ForbiddenHttpException('Login Required');
        }
    }
}
