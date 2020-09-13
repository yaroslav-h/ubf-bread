<?php

namespace app\models;

use app\components\ActiveRecord;
use app\components\helpers\StringHelper;
use app\models\edges\LessonReadByUser;
use Yii;
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
 * @property int|null $created_at
 * @property int|null $deleted_at
 *
 * @property Lesson $parent
 * @property Lesson[] $children
 */
class Lesson extends ActiveRecord
{

    private $_content;

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
    public function rules()
    {
        return [
            [['lang', 'date', 'title'], 'required'],
            [['lang', 'is_intro', 'created_at', 'deleted_at'], 'integer'],
            [['date'], 'safe'],
            [['content_json'], 'string'],
            [['title'], 'string', 'max' => 1024],
            [['passage_json'], 'string', 'max' => 128],
        ];
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

    public function getContent($section = null)
    {
        if(!is_array($this->_content)) {
            $this->_content = $this->content_json ? Json::decode($this->content_json) : ['v' => 1];
        }

        return $section ? ($this->_content[$section] ?? null) : $this->_content;
    }
    public function setContent($section = null, $value = null)
    {
        $this->getContent();
        if($section) {
            $this->_content[$section] = $value;
        }
        $this->content_json = Json::encode($this->_content);
    }

    public function getContent_key_verse() { return $this->getContent('a'); }
    public function setContent_key_verse($value) { $this->setContent('a', $value); }
    public function getContent_body() { return $this->getContent('b'); }
    public function setContent_body($value) { $this->setContent('b', $value); }
    public function getContent_prayer() { return $this->getContent('c'); }
    public function setContent_prayer($value) { $this->setContent('c', $value); }
    public function getContent_one_word() { return $this->getContent('d'); }
    public function setContent_one_word($value) { $this->setContent('d', $value); }
    public function getContent_version() { return $this->getContent('v'); }
    public function setContent_version($value) { $this->setContent('v', $value); }

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
            case 'user_reads':
            case 'notes': return 0;
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
                ->createCommand('select chapter, count(*) as total from (select JSON_UNQUOTE(JSON_EXTRACT(passage_json, \'$[0]\')) as chapter from lessons where lang=:lang and year(date)=:y and parent_id is null and deleted_at is null) as chapters group by chapter')
                ->bindParam(':y', $year)
                ->bindParam(':lang', $lang)
                ->queryAll();
        } else {
            $data = self::getDb()
                ->createCommand('select chapter, count(*) as total from (select JSON_UNQUOTE(JSON_EXTRACT(passage_json, \'$[0]\')) as chapter from lessons where lang=:lang and deleted_at is null) as chapters group by chapter')
                ->bindParam(':lang', $lang)
                ->queryAll();
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
