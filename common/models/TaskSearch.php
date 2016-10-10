<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Task;

/**
 * TaskSearch represents the model behind the search form of `common\models\Task`.
 */
class TaskSearch extends Task
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'tele_id', 'agent_id', 'created_at', 'updated_at', 'accident_id', 'woj', 'powiat', 'gmina', 'city', 'meeting', 'date'], 'integer'],
            [['victim_name', 'phone', 'qualified_name', 'details'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = Task::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'tele_id' => $this->tele_id,
            'agent_id' => $this->agent_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'accident_id' => $this->accident_id,
            'woj' => $this->woj,
            'powiat' => $this->powiat,
            'gmina' => $this->gmina,
            'city' => $this->city,
            'meeting' => $this->meeting,
            'date' => $this->date,
        ]);

        $query->andFilterWhere(['like', 'victim_name', $this->victim_name])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'qualified_name', $this->qualified_name])
            ->andFilterWhere(['like', 'details', $this->details]);

        return $dataProvider;
    }
}
