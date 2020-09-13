<?php


namespace res;


use res\models\User;
use Yii;
use yii\web\Response;

class Module extends \yii\base\Module
{

    public function init()
    {
        parent::init();

        response()->format = Response::FORMAT_JSON;
        request()->parsers['application/json'] = \yii\web\JsonParser::class;
    }

    public static function sharedState()
    {
        return [
            'user' => User::findOne(['id' => getMyId()]),
            'locale' => Yii::$app->language,
            'locales' => Yii::$app->params['availableLocales']
        ];
    }

    public static function initialState()
    {
        $data = self::sharedState();

        $data['csrf'] = Yii::$app->request->getCsrfToken(false);

        return $data;
    }

}