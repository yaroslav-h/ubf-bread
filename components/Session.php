<?php


namespace app\components;



use Yii;
use yii\redis\Connection;

class Session extends \yii\redis\Session
{

    public $keyPrefix = '_some_key';

    public function init()
    {
        $this->redis = new Connection([
            'hostname' => env('REDIS_HOST'),
            'port'     => env('REDIS_PORT'),
            'database' => 0,
        ]);

        parent::init();
    }

    public function writeSession($id, $data)
    {
        $result = parent::writeSession($id, $data);

        return $result;
    }


    /**
     * Get the timestamp of the start of the request with microsecond precision.
     */
    public function time()
    {
        return $_SERVER["REQUEST_TIME_FLOAT"];
    }
}
