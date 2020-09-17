<?php


namespace res\controllers;


use app\components\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use res\models\Testimony;
use res\Controller;

class TestimoniesController extends Controller
{

    public function access()
    {
        return [
            'rules' => [
                ['allow' => true, 'roles' => ['@']]
            ]
        ];
    }

    public function actionIndex($lesson = null, $user = null)
    {
        $query = Testimony::find()->published()->orderBy(['updated_at' => SORT_DESC]);

        if($lesson) $query->andLessonId($lesson);
        if($user) $query->andCreatedBy($user);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 99
            ],
            'sort' => false,
        ]);

        if(!$lesson && !$user) {
            $dataProvider->pagination = false;
        }

        return $dataProvider;
    }

    public function actionCreate()
    {
        $model = new Testimony();

        if($model->lesson_id && Testimony::find()->andLessonId($model->lesson_id)->my()->exists()) {
            throw new NotFoundHttpException;
        }

        if($model->load(post(), '') && $model->save()) {
            return $model;
        } elseif($model->hasErrors()) {
            return $this->unprocessable($model);
        }

        throw new ServerErrorHttpException;
    }

    public function actionView()
    {
        if($id = get('id')) {
            return Testimony::find()->andId($id)->published()->oneOrFail();
        } elseif($id = get('lesson')) {
            return Testimony::find()->andLessonId($id)->my()->oneOrFail();
        }

        throw new NotFoundHttpException;
    }

    public function actionUpdate($id)
    {
        $model = Testimony::find()->andId($id)->my()->oneOrFail();

        if($model->load(post(), '') && $model->save()) {
            return $model;
        } elseif($model->hasErrors()) {
            return $this->unprocessable($model);
        }

        throw new ServerErrorHttpException;
    }

    public function actionDelete($id)
    {
        if(Testimony::find()->andId($id)->my()->oneOrFail()->delete()) {
            return [];
        }

        throw new ServerErrorHttpException;
    }
}