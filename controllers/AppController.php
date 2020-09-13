<?php

namespace app\controllers;


use res\Module;
use Yii;
use yii\base\InvalidRouteException;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

class AppController extends Controller
{

    public function actionIndex()
    {
        return $this->app();
    }

    public function actionError()
    {
        if(($e = Yii::$app->getErrorHandler()->exception) && $e->getPrevious()) {
            if($e->getPrevious() instanceof InvalidRouteException) {
                return $this->app();
            }
        }

        return (new ErrorAction('error', $this))->run();
    }

    protected function app()
    {
        Yii::$app->response->headers->set('Cache-Control', 'no-cache');

        Yii::$app->response->setStatusCode(200);
        Yii::$app->response->format = Response::FORMAT_HTML;

        $app = file_get_contents(Yii::getAlias('@app/web/app.html'));

        $sharedData = Json::encode(Module::initialState());

        $app = implode("_sharedData = $sharedData", explode("_sharedData = null", $app));

        if($app == null) throw new ServerErrorHttpException('Unable to create APP.');

        Yii::$app->response->content = $app;

        return null;
    }
}
