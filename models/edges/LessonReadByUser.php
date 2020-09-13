<?php

namespace app\models\edges;

use app\models\Lesson;
use app\models\User;

/**
 * This is the model class for table "lesson_read_by_user".
 *
 * @property int $lesson_id
 * @property int $user_id
 * @property int $read_at
 * @property int $lang
 *
 * @property Lesson $lesson
 * @property User $user
 */
class LessonReadByUser extends \yii\db\ActiveRecord implements EdgeInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lesson_read_by_user';
    }

    /**
     * Gets query for [[Lesson]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLesson()
    {
        return $this->hasOne(Lesson::className(), ['id' => 'lesson_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function create($id1, $id2, $params = [])
    {
        $edge = new self(['lesson_id' => $id1, 'user_id' => $id2, 'read_at' => time()]);
        $edge->setAttributes($params, false);
        if($edge->save(false)) {
            Lesson::updateAllCounters(['user_reads_count' => 1], ['id' => $id1]);
            return true;
        }

        return false;
    }

    public static function remove($id1, $id2, $params = [])
    {
        if(self::deleteAll(['lesson_id' => $id1, 'user_id' => $id2])) {
            Lesson::updateAllCounters(['user_reads_count' => -1], ['id' => $id1]);
            return true;
        }

        return false;
    }
}
