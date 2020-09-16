<?php


namespace res\models;

use app\components\helpers\StringHelper;
use app\models\edges\LessonReadByUser;
use app\models\Testimony;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * @property Lesson $translation
 */
class Lesson extends \app\models\Lesson
{

    protected static $_is_read = [];
    protected static $_is_passed = [];

    public static function prepareBooleanValues($models)
    {
        $ids = ArrayHelper::getColumn($models, 'id');

        $read_lessons_ids = getMyId() ? LessonReadByUser::find()->select('lesson_id')->where(['lesson_id' => $ids, 'user_id' => getMyId()])->column() : [];
        $pass_lessons_ids = getMyId() ? Testimony::find()->select('id, lesson_id')->andWhere(['lesson_id' => $ids, 'created_by' => getMyId()])->published()->indexBy('lesson_id')->all() : [];

        foreach ($ids as $id) {
            self::$_is_read[$id] = in_array($id, $read_lessons_ids);
            self::$_is_passed[$id] = ArrayHelper::getValue($pass_lessons_ids[$id] ?? [], 'id');
        }
    }

    public function fields()
    {
        $t = $this->translation ? $this->translation->toArray(['lang', 'title', 'passage','content']) : [];

        return [
            'id' => function() {
                return (string)$this->id;
            },
            'lang' => function() use ($t) {
                return $t['lang'] ?? $this->lang;
            },
            'date',
            'title' => function() use ($t) {
                return $t['title'] ?? $this->title;
            },
            'passage' => function() use ($t) {
                return $t['passage'] ?? $this->passage;
            },
            'passage_link' => function() use ($t) {
                return StringHelper::getLink2Passage($t['passage'] ?? $this->passage);
            },
            'content' => function() use ($t) {
                return $t['content'] ?? [
                    'key_verse' => $this->content_key_verse,
                    'body' => $this->content_body,
                    'prayer' => $this->content_prayer,
                    'one_word' => $this->content_one_word,
                    'version' => $this->content_version
                ];
            },
            'is_intro' => function() {
                return $this->is_intro == 1;
            },
            'is_read' => function() {
                return self::$_is_read[$this->id] ?? LessonReadByUser::find()->where(['lesson_id' => $this->id, 'user_id' => getMyId()])->exists();
            },
            'is_passed' => function() {
                return (self::$_is_passed[$this->id] ?? $this->getIsPassed()) != null;
            },
            'passed_id' => function() {
                return self::$_is_passed[$this->id] ?? $this->getPassedId();
            },
            'user_reads_count',
            'testimonies_count',
        ];
    }

    public function getPassedId($createdBy = null)
    {
        $createdBy = $createdBy ?: getMyId();

        return Testimony::find()
            ->select('id')
            ->andLessonId($this->id)
            ->andCreatedBy($createdBy)
            ->scalar() ?: null;
    }

    public function getIsPassed($createdBy = null)
    {
        $createdBy = $createdBy ?: getMyId();

        return Testimony::find()
            ->select('id')
            ->andLessonId($this->id)
            ->andCreatedBy($createdBy)
            ->published()
            ->scalar() ?: null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslation()
    {
        return $this->hasOne(static::class, ['parent_id' => 'id']);
    }

    /**
     * @param $name
     * @return Query
     */
    public static function findByPassageName($name)
    {
        return (new Query())
            ->select(['id' => 'if(parent_id is null, id, parent_id)'])
            ->from(Lesson::tableName())
            ->where("passage_json like :name", [':name' => '["' . $name . '%']);
    }
}