<?php


namespace app\components\helpers;


use yii\httpclient\Client;

class RecaptchaHelper
{
    public static function isVerified($response)
    {
        if(empty(env('RECAPTCHA_V3_SECRET_KEY'))) {
            return true;
        }

        if(empty($response)) {
            return false;
        }

        $httpClient = new Client();
        $r = $httpClient->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('RECAPTCHA_V3_SECRET_KEY'),
            'response' => $response
        ]);

        $res = $r->send();

        if($res->data['success'] == true) {
            return true;
        }

        return false;
    }

}
