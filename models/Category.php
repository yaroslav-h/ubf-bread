<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "categories".
 *
 * @property int $id
 * @property int|null $parent_id
 * @property int $type
 * @property int $lang
 * @property string $name
 * @property int $alt
 * @property int $order
 *
 * @property Category $parent
 * @property Category[] $children
 * @property Lesson[] $lessons
 */
class Category extends \yii\db\ActiveRecord
{
    const TYPE_CHAPTER = 1;

    const ALT_DEFAULT = 0;

    public static function types()
    {
        return [
            self::TYPE_CHAPTER => Yii::t('app', 'Chapters'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'categories';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_id', 'type', 'lang', 'alt', 'order'], 'integer'],
            [['lang', 'name'], 'required'],
            [['name'], 'string', 'max' => 128],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['parent_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'parent_id' => Yii::t('app', 'Parent ID'),
            'type' => Yii::t('app', 'Type'),
            'lang' => Yii::t('app', 'Lang'),
            'name' => Yii::t('app', 'Name'),
            'alt' => Yii::t('app', 'Alt'),
            'order' => Yii::t('app', 'Order'),
        ];
    }

    /**
     * Gets query for [[Parent]].
     *
     * @return \yii\db\ActiveQuery|\app\models\queries\CategoryQuery
     */
    public function getParent()
    {
        return $this->hasOne(Category::className(), ['id' => 'parent_id']);
    }

    /**
     * Gets query for [[Categories]].
     *
     * @return \yii\db\ActiveQuery|\app\models\queries\CategoryQuery
     */
    public function getChildren()
    {
        return $this->hasMany(Category::className(), ['parent_id' => 'id']);
    }

    /**
     * Gets query for [[Lessons]].
     *
     * @return \yii\db\ActiveQuery|\app\models\queries\LessonQuery
     */
    public function getLessons()
    {
        return $this->hasMany(Lesson::className(), ['chapter_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\queries\CategoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\queries\CategoryQuery(get_called_class());
    }

    public function getLangs()
    {
        if($this->parent_id) {
            return [$this->lang];
        }

        return array_merge([$this->lang], array_map(function($c) { return $c->lang; }, array_filter($this->children, function($c) {
            return $c->alt == $this->alt;
        })));
    }

    /**
     * @return Category[]
     */
    public function getTranslations()
    {
        if($this->parent_id) {
            return [];
        }

        return array_filter($this->children, function($c) {
            return $c->alt == $this->alt;
        });
    }
}
