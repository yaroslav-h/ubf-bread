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
                'updatedAtAttribute' => false
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
            [['content_body', 'content_prayer', 'content_one_word',], 'string']
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
            'created_by',
            'createdBy' => function() {
                return $this->createdBy->toArray(['id', 'name']);
            },
            'created_at'
        ];
    }
}