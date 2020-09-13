<?php

namespace admin\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\Lesson;
use yii\helpers\ArrayHelper;

/**
 * LessonSearch represents the model behind the search form of `app\models\Lesson`.
 */
class LessonSearch extends Lesson
{

    public $tab = 'upcoming';

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'lang', 'is_intro', 'created_at', 'deleted_at'], 'integer'],
            [['tab'], 'in', 'range' => ArrayHelper::getColumn(self::tabs(), 'key')],
            [['date', 'title', 'passage_json', 'content_json'], 'safe'],
        ];
    }

    public static function tabs()
    {
        return [
            ['label' => \Yii::t('app', 'All'), 'key' => 'all'],
            ['label' => \Yii::t('app', 'Upcoming'), 'key' => 'upcoming'],
            ['label' => \Yii::t('app', 'Passed'), 'key' => 'passed'],
            ['label' => \Yii::t('app', 'Deleted'), 'key' => 'deleted'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Lesson::find()->alias('t')->with('children')->andWhere('t.parent_id is null');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->tab = $params['tab'] ?? $this->tab;
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if($this->tab === 'upcoming') {
            $query->orderBy(['t.date' => SORT_ASC])->andWhere(['>=', 't.date', date('Y-m-d')])->andWhere('t.deleted_at is null');
        } elseif($this->tab === 'passed') {
            $query->orderBy(['t.date' => SORT_DESC])->andWhere(['<', 't.date', date('Y-m-d')])->andWhere('t.deleted_at is null');
        } elseif($this->tab === 'deleted') {
            $query->orderBy(['t.deleted_at' => SORT_DESC])->andWhere(['>', 't.deleted_at', 0]);
        } else {
            $query->andWhere('t.deleted_at is null');
        }

        // grid filtering conditions
        $query->andFilterWhere([
            't.id' => $this->id,
            't.lang' => $this->lang,
            't.date' => $this->date,
            't.is_intro' => $this->is_intro,
            't.created_at' => $this->created_at,
            't.deleted_at' => $this->deleted_at,
        ]);


        if($q = request()->get('q')) {
            $query->orFilterWhere(['like', 't.title', $q])
                ->orFilterWhere(['like', 't.passage_json', $q]);
        }

        return $dataProvider;
    }
}
