<?php

namespace app\models;

use app\models\traits\ContentJsonAttribute;
use Yii;

/**
 * This is the model class for table "testimonies".
 *
 * @property int $id
 * @property int $lesson_id
 * @property string|null $content_json
 * @property int $is_published
 * @property int $created_by
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property User $createdBy
 * @property Lesson $lesson
 */
class Testimony extends \yii\db\ActiveRecord
{
    use ContentJsonAttribute;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'testimonies';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'lesson_id' => Yii::t('app', 'Lesson'),
            'content_json' => Yii::t('app', 'Content Json'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    public function contentFields()
    {
        return [
            'b' => 'content_body',
            'c' => 'content_prayer',
            'd' => 'content_one_word',
            'v' => 'content_version',
        ];
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

    /**
     * {@inheritdoc}
     * @return \app\models\queries\TestimonyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\queries\TestimonyQuery(get_called_class());
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if($insert) {
            $this->lesson->updateCounters(['testimonies_count' => 1]);
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();

        $this->lesson->updateCounters(['testimonies_count' => -1]);
    }
}
