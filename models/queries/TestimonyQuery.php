<?php

namespace app\models\queries;

use yii\web\NotFoundHttpException;

/**
 * This is the ActiveQuery class for [[\app\models\Testimony]].
 *
 * @see \app\models\Testimony
 */
class TestimonyQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    public function published($alias = null)
    {
        return $this->andWhere([
            ($alias ? $alias . "." : "") . "is_published" => 1
        ]);
    }

    public function andId($id, $alias = null)
    {
        return $this->andWhere([
            ($alias ? $alias . "." : "") . "id" => $id
        ]);
    }

    public function andLessonId($lessonId, $alias = null)
    {
        return $this->andWhere([
            ($alias ? $alias . "." : "") . "lesson_id" => $lessonId
        ]);
    }

    public function andCreatedBy($id, $alias = null)
    {
        return $this->andWhere([
            ($alias ? $alias . "." : "") . "created_by" => $id
        ]);
    }

    public function my($alias = null)
    {
        return $this->andWhere([
            ($alias ? $alias . "." : "") . "created_by" => getMyId()
        ]);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\Testimony[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\Testimony|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
    /**
     * @return \app\models\Testimony|array
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
