<?php

namespace res;


use Yii;
use yii\base\Model;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;

class Controller extends \yii\web\Controller
{
    /**
     * @var string|array the configuration for creating the serializer that formats the response data.
     */
    public $serializer = 'yii\rest\Serializer';

    public function behaviors()
    {
        $behaviors = [];

        if($this->access()) {
            $behaviors['access'] = array_merge(['class' => AccessControl::class], $this->access());
        }

        if($this->verbs()) {
            $behaviors['verbFilter'] = [
                'class' => VerbFilter::class,
                'actions' => $this->verbs(),
            ];
        }

        return $behaviors;
    }


    public function unprocessable(Model $model)
    {
        \Yii::$app->response->statusCode = 422;

        return $model->getFirstErrors();
    }

    public function access()
    {
        return [];
    }

    protected function verbs()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
        return $this->serializeData($result);
    }

    /**
     * Serializes the specified data.
     * The default implementation will create a serializer based on the configuration given by [[serializer]].
     * It then uses the serializer to serialize the given data.
     * @param mixed $data the data to be serialized
     * @return mixed the serialized data.
     */
    protected function serializeData($data)
    {
        return Yii::createObject($this->serializer)->serialize($data);
    }
}