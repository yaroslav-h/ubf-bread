<?php


namespace app\components;


class ActiveDataProvider extends \yii\data\ActiveDataProvider
{
    public $afterPrepare;

    protected function prepareModels()
    {
        $models = parent::prepareModels();

        if($this->afterPrepare instanceof \Closure) {
            call_user_func_array($this->afterPrepare, [$models, $this]);
        }

        return $models;
    }

}