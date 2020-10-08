<?php


namespace app\components\actions;


use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Cookie;

class SetLocale extends Action
{

    public $cookieName = '_locale';

    public $callback;

    public function run()
    {
        $locale = get('locale');
        $languages = ArrayHelper::map(Yii::$app->params['availableLocales'], 'locale', 'locale');

        if (!isset($languages[$locale])) {
            throw new BadRequestHttpException;
        }

        Yii::$app->language = $languages[$locale];

        $cookie = new Cookie([
            'name' => $this->cookieName,
            'value' => Yii::$app->language,
            'expire' => time() + 999999
        ]);

        response()->cookies->add($cookie);

        if($this->callback instanceof \Closure) {
            call_user_func_array($this->callback, [$locale]);
        }

        return '';
    }

}