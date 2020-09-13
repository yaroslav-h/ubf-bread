<?php

namespace app\models\queries;

use yii\web\NotFoundHttpException;

/**
 * This is the ActiveQuery class for [[\app\models\Lesson]].
 *
 * @see \app\models\Lesson
 */
class LessonQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    public function widthTranslation($lang)
    {
        return $this->with([
            'translation' => function($q) use ($lang) {
                $q->andWhere(['lang' => $lang]);
            }
        ]);
    }

    public function andId($id, $alias = null)
    {
        return $this->andWhere([
            ($alias ? $alias . "." : "") . "id" => $id
        ]);
    }

    public function andDate($date, $alias = null)
    {
        return $this->andWhere([
            ($alias ? $alias . "." : "") . "date" => $date
        ]);
    }

    public function parentIsNull($alias = null)
    {
        return $this->andWhere(($alias ? $alias . "." : "") . "parent_id is null");
    }

    /**
     * {@inheritdoc}
     * @return \app\models\Lesson[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\Lesson|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @return \app\models\Lesson|array
     */
    public function oneOrFail($db = null)
    {
        if($model = parent::one($db)) {
            return $model;
        } else {
            throw new NotFoundHttpException;
        }
    }
}
