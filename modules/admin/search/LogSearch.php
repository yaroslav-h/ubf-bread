<?php

namespace admin\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\Log;
use yii\helpers\ArrayHelper;
use yii\log\Logger;

/**
 * LogSearch represents the model behind the search form of `admin\models\Log`.
 */
class LogSearch extends Log
{
    public $date;
    public $tab = Logger::LEVEL_ERROR;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'level'], 'integer'],
            [['category', 'prefix', 'message'], 'safe'],
            ['tab', 'in', 'range' => ArrayHelper::getColumn(self::tabs(), 'key')],
            [['log_time'], 'number'],
            ['date', 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public static function tabs()
    {
        return [
            ['label' => Yii::t('app', 'Error') . " (".self::totalCount(Logger::LEVEL_ERROR).")", 'key' => Logger::LEVEL_ERROR],
            ['label' => Yii::t('app', 'Warning') . " (".self::totalCount(Logger::LEVEL_WARNING).")", 'key' => Logger::LEVEL_WARNING],
            ['label' => Yii::t('app', 'Info') . " (".self::totalCount(Logger::LEVEL_INFO).")", 'key' => Logger::LEVEL_INFO],
            ['label' => Yii::t('app', 'Trace') . " (".self::totalCount(Logger::LEVEL_TRACE).")", 'key' => Logger::LEVEL_TRACE],
            ['label' => Yii::t('app', 'Other') . " (".self::totalCount('other').")", 'key' => 'other'],
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
        $query = Log::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->sort->defaultOrder = ['log_time' => SORT_DESC];
        $this->tab = $params['tab'] ?? $this->tab;
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if($this->tab != 'other') {
            $query->andWhere(['level' => $this->tab]);
        } else {
            $query->andWhere(['not in', 'level', ArrayHelper::getColumn(self::tabs(), 'key')]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'level' => $this->level,
            'category' => $this->category,
        ]);

        if($this->date) {
            $query->andWhere([
                'between', 'log_time',
                Yii::$app->formatter->asTimestamp($this->date . " 00:00:00"),
                Yii::$app->formatter->asTimestamp($this->date . " 23:59:59"),
            ]);
        }

        $query->andFilterWhere(['like', 'category', $this->category])
            ->andFilterWhere(['like', 'prefix', $this->prefix])
            ->andFilterWhere(['like', 'message', $this->message]);

        if($q = request()->get('q')) {
            $query->orFilterWhere(['like', 't.category', $q])
                ->orFilterWhere(['like', 't.prefix', $q])
                ->orFilterWhere(['like', 't.message', $q]);
        }

        return $dataProvider;
    }

    public function getCategories()
    {
        if($this->tab != 'other') {
            return self::find()->cache(3600)
                ->select('category')
                ->andWhere(['level' => $this->tab])
                ->orderBy('category')
                ->groupBy('category')
                ->indexBy('category')
                ->column();
        }

        return self::find()->cache(3600)
            ->select('category')
            ->andWhere(['not in', 'level', ArrayHelper::getColumn(self::tabs(), 'key')])
            ->orderBy('category')
            ->groupBy('category')
            ->indexBy('category')
            ->column();
    }
}
