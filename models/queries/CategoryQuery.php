<?php

namespace app\models\queries;

use app\models\Category;

/**
 * This is the ActiveQuery class for [[\app\models\Category]].
 *
 * @see \app\models\Category
 */
class CategoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    public function chapters($alias = null)
    {
        return $this->andWhere([
            ($alias ? $alias . "." : "") . "type" => Category::TYPE_CHAPTER,
            ($alias ? $alias . "." : "") . "alt" => Category::ALT_DEFAULT,
        ]);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\Category[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\Category|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
