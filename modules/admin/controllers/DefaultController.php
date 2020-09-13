<?php


namespace admin\controllers;


use app\models\forms\ChangePasswordForm;
use admin\models\forms\LoginForm;
use app\rbac\RbacEnum;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ErrorAction;

class DefaultController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'logout', 'change-password'],
                'rules' => [
                    ['allow' => true, 'roles' => [RbacEnum::MODER]],
                    ['allow' => true, 'roles' => ['@'], 'actions' => ['logout']]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => ErrorAction::class,
        ];
    }

    public function beforeAction($action)
    {
        if($action->id == 'error') {
            $this->layout = 'guest';
        }
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        return render('index');
    }

    public function actionLogin()
    {
        $this->layout = 'login';
        $model = new LoginForm();

        if($model->load(post()) && $model->login()) {
            return redirect(\Yii::$app->homeUrl);
        }

        return render('login', compact('model'));
    }

    public function actionLogout()
    {
        \Yii::$app->user->logout();

        return redirect(\Yii::$app->homeUrl);
    }

    public function actionChangePassword()
    {
        $model = new ChangePasswordForm();

        if($model->load(post()) && $model->save()) {
            return redirect(request()->get('return', \Yii::$app->homeUrl));
        }

        return render('change-password', compact('model'));
    }
}