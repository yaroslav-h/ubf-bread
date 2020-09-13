<?php


namespace admin\models;


use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\HtmlPurifier;

class Lesson extends \app\models\Lesson
{
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
            [
                'class' => BlameableBehavior::class,
                'updatedByAttribute' => false,
            ]
        ];
    }

    public function rules()
    {
        return [
            [['lang', 'date', 'title', 'passage', 'content_body'], 'required'],
            [['title', 'passage', 'content_body', 'content_key_verse', 'content_prayer', 'content_one_word'], 'trim', 'skipOnEmpty' => true],
            [['title', 'passage', 'content_key_verse', 'content_prayer', 'content_one_word'], function($attr) {
                $this->$attr = HtmlPurifier::process($this->$attr);
            }],
            ['lang', 'in', 'range' => array_keys(self::availableLocales())],
            [['title', 'content_one_word'], 'string', 'max' => 250],
            [['passage'], 'string', 'max' => 100],
            ['date', 'date', 'format' => 'php:Y-m-d'],
            ['parent_id', 'integer'],
            ['is_intro', 'boolean'],
        ];
    }

}