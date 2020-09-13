<?php


namespace admin;


use yii\web\ErrorHandler;

class Module extends \yii\base\Module
{
    public $layout = 'main';

    public function init()
    {
        parent::init();

        \Yii::$app->user->loginUrl = ['admin/default/login'];
        \Yii::$app->homeUrl = '/admin/default/index';
        \Yii::configure($this, [
            'components' => [
                'errorHandler' => [
                    'class' => ErrorHandler::className(),
                    'errorAction' => 'admin/default/error',
                ]
            ],
        ]);

        /** @var ErrorHandler $handler */
        $handler = $this->get('errorHandler');
        \Yii::$app->set('errorHandler', $handler);
        $handler->register();
    }
}