<?php

namespace admin\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Category;

/**
 * CategorySearch represents the model behind the search form of `app\models\Category`.
 */
class CategorySearch extends Category
{
    public $tab = self::TYPE_CHAPTER;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'parent_id', 'type', 'lang', 'alt', 'order'], 'integer'],
            [['name'], 'safe'],
        ];
    }

    public static function tabs()
    {
        $tabs = [];

        foreach (self::types() as $key => $name) {
            $tabs[] = ['label' => $name, 'key' => $key];
        }

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
        $query = Category::find()
            ->alias('t')
            ->with('children')
            ->andWhere('t.parent_id is null')
            ->andWhere(['t.alt' => self::ALT_DEFAULT])
            ->orderBy(['t.order' => SORT_ASC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => false
        ]);

        $this->tab = $params['tab'] ?? $this->tab;
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andWhere(['t.type' => $this->tab]);

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'type' => $this->type,
            'lang' => $this->lang,
            'alt' => $this->alt,
            'order' => $this->order,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
