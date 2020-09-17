<?php


namespace res\models;


use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

class Testimony extends \app\models\Testimony
{

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
            ],
            [
                'class' => BlameableBehavior::class,
                'updatedByAttribute' => false
            ]
        ];
    }

    public function rules()
    {
        return [
            [['lesson_id', 'content_body'], 'required'],
            [['lesson_id'], 'integer'],
            [['content_body', 'content_prayer', 'content_one_word',], 'string'],
            [['is_published',], 'boolean'],
        ];
    }

    public function fields()
    {
        return [
            'id',
            'lesson_id',
            'content' => function() {
                return [
                    'body' => $this->content_body,
                    'prayer' => $this->content_prayer,
                    'one_word' => $this->content_one_word,
                    'version' => $this->content_version
                ];
            },
            'is_published' => function() {
                return $this->is_published == 1;
            },
            'created_by',
            'createdBy' => function() {
                return $this->createdBy->toArray(['id', 'name']);
            },
            'created_at',
            'updated_at',
        ];
    }

    public function extraFields()
    {
        return [
            'lesson' => function() {
                return $this->lesson->toArray();
            },
        ];
    }

    public function beforeSave($insert)
    {
        return parent::beforeSave($insert);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[Lesson]].
     *
     * @return \yii\db\ActiveQuery|\app\models\queries\LessonQuery
     */
    public function getLesson()
    {
        return $this->hasOne(Lesson::className(), ['id' => 'lesson_id']);
    }
}