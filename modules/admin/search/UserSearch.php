<?php

namespace admin\search;

use app\rbac\RbacEnum;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\User;
use yii\helpers\ArrayHelper;

/**
 * UserSearch represents the model behind the search form of `admin\models\User`.
 */
class UserSearch extends User
{

    public $tab = 'all';

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'deleted_at'], 'integer'],
            ['tab', 'in', 'range' => ArrayHelper::getColumn(self::tabs(), 'key')],
            [['name', 'email', 'password_hash', 'password_reset_token', 'auth_key'], 'safe'],
        ];
    }

    public static function tabs()
    {
        $tabs = [
            ['label' => Yii::t('app', 'All'), 'key' => 'all'],
        ];

        foreach (RbacEnum::groups() as $group => $name) {
            $tabs[] = ['label' => $name, 'key' => $group];
        }

        $tabs[] = ['label' => Yii::t('app', 'Deleted'), 'key' => 'deleted'];
        return $tabs;
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
        $query = User::find()->alias('t');

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

        if($this->tab == 'deleted') {
            $query->andWhere('t.deleted_at is not null');
            $dataProvider->sort->defaultOrder = ['deleted_at' => SORT_DESC];
        } elseif(in_array($this->tab, array_keys(RbacEnum::groups()))) {
            $query->andWhere('t.deleted_at is null');
            $query->andWhere(['t.group' => $this->tab]);
            $dataProvider->sort->defaultOrder = ['created_at' => SORT_DESC];
        } else {
            $query->andWhere('t.deleted_at is null');
            $dataProvider->sort->defaultOrder = ['created_at' => SORT_DESC];
        }

        // grid filtering conditions
        $query->andFilterWhere([
            't.id' => $this->id,
            't.created_at' => $this->created_at,
            't.deleted_at' => $this->deleted_at,
        ]);

        $query->andFilterWhere(['like', 't.name', $this->name])
            ->andFilterWhere(['like', 't.email', $this->email]);

        if($q = request()->get('q')) {
            $query->orFilterWhere(['like', 't.name', $q])
                ->orFilterWhere(['like', 't.email', $q]);
        }

        return $dataProvider;
    }
}
