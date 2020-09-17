<?php


namespace res\controllers;


use app\components\actions\SetLocale;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;
use res\Module;
use res\Controller;
use res\forms\LoginForm;
use res\forms\SignupForm;
use res\forms\RecoverPasswordForm;

class SiteController extends Controller
{

    public function access()
    {
        return [
            'only' => ['login', 'signup', 'logout'],
            'rules' => [
                [
                    'actions' => ['login', 'signup'],
                    'allow' => true,
                    'roles' => ['?'],
                ],
                [
                    'actions' => ['logout'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];
    }

    public function verbs()
    {
        return [
            'login'  => ['post'],
            'signup' => ['post'],
            'logout' => ['post'],
            'recover-password' => ['post'],
        ];
    }

    public function actions()
    {
        return [
            'set-locale' => SetLocale::class
        ];
    }

    public function actionRecoverPassword()
    {
        $model = new RecoverPasswordForm();

        if($model->load(Yii::$app->request->post(), '') && $model->save()) {
            return [
                'step' => $model->step,
            ];
        } elseif($model->hasErrors()) {
            Yii::$app->response->setStatusCode(422);
            return $model->getFirstErrors();
        } else {
            throw new ServerErrorHttpException('Unable to recover password.');
        }
    }

    public function actionVerses($passage)
    {
        $baseUrls = [
            LOCALE_UK_UA => 'http://bible.ubf.org.ua/ukr/?scr=',
            LOCALE_EN_US => 'http://bible.ubf.org.ua/ukr/?scr=',
            LOCALE_RU_RU => 'http://bible.ubf.org.ua/rst/?scr=',
        ];

        $html = file_get_contents($baseUrls[lang()] . urlencode($passage));

        $doc = new \DOMDocument();
        $doc->loadHTML($html);
        $mainText = $doc->getElementById('mainText');
        $childNodeList = $mainText->getElementsByTagName('div');

        return [
            'content' => $childNodeList->length ? $childNodeList->item(0)->nodeValue : false
        ];
    }

    public function actionLogin()
    {
        $model = new LoginForm();

        if($model->load(post(), '') && $model->login()) {
            return Module::initialState();
        } elseif($model->hasErrors()) {
            return $this->unprocessable($model);
        } else {
            throw new ServerErrorHttpException('Unable to login.');
        }
    }

    public function actionSignup()
    {
        $model = new SignupForm();

        if($model->load(Yii::$app->request->post(), '') && $model->signup()) {
            return isGuest() ? [] : Module::sharedState();
        } elseif($model->hasErrors()) {
            return $this->unprocessable($model);
        }

        throw new ServerErrorHttpException('Unable to signup.');
    }

    public function actionLogout()
    {
        return Yii::$app->user->logout();
    }

    public function actionInitialState()
    {
        return Module::initialState();
    }
}
