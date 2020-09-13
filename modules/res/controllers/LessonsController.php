<?php


namespace res\controllers;


use res\Controller;
use res\models\Lesson;
use app\components\ActiveDataProvider;
use yii\db\Query;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;

class LessonsController extends Controller
{
    public function actionToday()
    {
        return new ActiveDataProvider([
            'afterPrepare' => function ($models) {
                Lesson::prepareBooleanValues($models);
            },
            'query' => Lesson::find()->widthTranslation(lang())->andDate(date('Y-m-d'))->parentIsNull()->orderBy('id'),
            'pagination' => false,
            'sort' => false,
        ]);
    }

    public function actionDate($date)
    {
        return new ActiveDataProvider([
            'afterPrepare' => function ($models) {
                Lesson::prepareBooleanValues($models);
            },
            'query' => Lesson::find()->widthTranslation(lang())->andDate($date)->parentIsNull()->orderBy('id'),
            'pagination' => false,
            'sort' => false,
        ]);
    }

    public function actionMonth($date)
    {
        return new ActiveDataProvider([
            'afterPrepare' => function ($models) {
                Lesson::prepareBooleanValues($models);
            },
            'query' => Lesson::find()->widthTranslation(lang())
                ->andWhere([
                    'and',
                    ['>=', 'date', date("Y-m-01", strtotime($date))],
                    ['<=', 'date', date("Y-m-t", strtotime($date))]
                ])
                ->parentIsNull()->orderBy('id'),
            'pagination' => false,
            'sort' => false,
        ]);
    }

    public function actionChapter($name)
    {
        return new ActiveDataProvider([
            'afterPrepare' => function ($models) {
                Lesson::prepareBooleanValues($models);
            },
            'query' => Lesson::find()->widthTranslation(lang())
                ->andWhere([
                    'id' => Lesson::findByPassageName($name)
                ])
                ->parentIsNull()->orderBy('id'),
            'pagination' => false,
            'sort' => false,
        ]);
    }

    public function actionView($id)
    {
        return $this->findModel($id);
    }

    public function actionGetChapters($year = null)
    {
        return Lesson::getChapters($year);
    }
    public function actionGetYears()
    {
        return Lesson::getYears();
    }

    public function actionMarkAsRead($id)
    {
        if(isGuest()) throw new ForbiddenHttpException;

        if($this->findModel($id)->markAsRead()) {
            return [];
        }

        throw new ServerErrorHttpException;
    }

    /**
     * @param $id
     * @return \app\models\Lesson|array
     * @throws \yii\web\NotFoundHttpException
     */
    protected function findModel($id)
    {
        return Lesson::find()->widthTranslation(lang())->andId($id)->parentIsNull()->oneOrFail();
    }
}