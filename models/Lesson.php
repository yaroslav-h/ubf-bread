<?php

namespace app\models;

use app\components\ActiveRecord;
use app\components\helpers\StringHelper;
use app\models\edges\LessonReadByUser;
use app\models\traits\ContentJsonAttribute;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * This is the model class for table "lessons".
 *
 * @property int $id
 * @property int $lang
 * @property string $date
 * @property string $title
 * @property string|null $passage
 * @property string|null $passage_json
 * @property string|null $content_json
 * @property string|null $content_key_verse
 * @property string|null $content_body
 * @property string|null $content_prayer
 * @property string|null $content_one_word
 * @property string|null $content_version
 * @property int $is_intro
 * @property int $testimonies_count
 * @property int $user_reads_count
 * @property int|null $created_at
 * @property int|null $deleted_at
 *
 * @property Lesson $parent
 * @property Lesson[] $children
 */
class Lesson extends ActiveRecord
{

    use ContentJsonAttribute;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lessons';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'lang' => Yii::t('app', 'Lang'),
            'date' => Yii::t('app', 'Date'),
            'title' => Yii::t('app', 'Title'),
            'passage' => Yii::t('app', 'Passage'),
            'content_json' => Yii::t('app', 'Content Json'),
            'content_key_verse' => Yii::t('app', 'Key verse'),
            'content_body' => Yii::t('app', 'Body'),
            'content_prayer' => Yii::t('app', 'Prayer'),
            'content_one_word' => Yii::t('app', 'One word'),
            'is_intro' => Yii::t('app', 'Is Intro'),
            'created_at' => Yii::t('app', 'Created At'),
            'deleted_at' => Yii::t('app', 'Deleted At'),
        ];
    }

    public function contentFields()
    {
        return [
            'a' => 'content_key_verse',
            'b' => 'content_body',
            'c' => 'content_prayer',
            'd' => 'content_one_word',
            'v' => 'content_version',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\models\queries\LessonQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\queries\LessonQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(self::class, ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(self::class, ['parent_id' => 'id']);
    }

    public function getLangs()
    {
        if($this->parent_id) {
            return [$this->lang];
        }

        return array_merge([$this->lang], array_map(function($c) { return $c->lang; }, $this->children));
    }

    public function getPassage()
    {
        return StringHelper::toHumanReadablePassage($this->passage_json);
    }

    public function setPassage($value)
    {
        $this->passage_json = StringHelper::fromHumanReadablePassage($value);
    }

    public static function availableLocales()
    {
        return Yii::$app->params['locales'];
    }

    public function getAvailableLocales()
    {
        $langs = self::availableLocales();

        if(!$this->isNewRecord) {
            foreach ($this->getLangs() as $lang) {
                unset($langs[$lang]);
            }
        }

        return $langs;
    }

    public static function resolveTotalCount($key)
    {
        switch ($key) {
            case 'reads': return LessonReadByUser::find()->count();
            case 'testimonies': return Testimony::find()->count();
            case 'default': return self::find()->andWhere('parent_id is null and deleted_at is null')->count();
        }

        return -1;
    }

    public function markAsRead($user_id = null)
    {
        $user_id = $user_id ?: getMyId();
        return LessonReadByUser::create($this->id, $user_id, [
            'lang' => lang()
        ]);
    }

    public static function getChapters($year)
    {
        $lang = lang();

        if($year) {
            $data = self::getDb()
                ->createCommand('select lang, chapter, count(*) as total from (select lang, JSON_UNQUOTE(JSON_EXTRACT(passage_json, \'$[0]\')) as chapter from lessons where year(date)=:y and deleted_at is null) as t group by lang, chapter')
                ->bindParam(':y', $year)
                ->queryAll();
        } else {
            $data = self::getDb()
                ->createCommand('select lang, chapter, count(*) as total from (select lang, JSON_UNQUOTE(JSON_EXTRACT(passage_json, \'$[0]\')) as chapter from lessons where deleted_at is null) as chapters group by lang, chapter')
                ->bindParam(':lang', $lang)
                ->queryAll();
        }

        if($data) {
            $langs = array_values(ArrayHelper::map($data, 'lang', 'lang'));
            $showLang = in_array($lang, $langs) ? $lang : $langs[0];
            $data = array_filter($data, function ($item) use ($showLang) {
                return $item['lang'] == $showLang;
            });
            $data = array_values($data);
        }

        return $data;
    }

    public static function getYears()
    {
        $data = self::getDb()
            ->createCommand('select `year`, count(*) as total from (select year(date) as `year` from lessons where parent_id is null and deleted_at is null) as y group by `year`')
            ->queryAll();

        return $data;
    }
}
