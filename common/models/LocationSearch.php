<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Location;

/**
 * LocationSearch represents the model behind the search form of `common\models\Location`.
 */
class LocationSearch extends Location
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['WOJ', 'POW', 'GMI', 'RM', 'MZ', 'NAZWA', 'SYM', 'SYMPOD', 'STAN_NA'], 'safe'],
            [['RODZ_GMI'], 'integer'],
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
        $query = Location::find();

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
            'RODZ_GMI' => $this->RODZ_GMI,
            'STAN_NA' => $this->STAN_NA,
        ]);

        $query->andFilterWhere(['like', 'WOJ', $this->WOJ])
            ->andFilterWhere(['like', 'POW', $this->POW])
            ->andFilterWhere(['like', 'GMI', $this->GMI])
            ->andFilterWhere(['like', 'RM', $this->RM])
            ->andFilterWhere(['like', 'MZ', $this->MZ])
            ->andFilterWhere(['like', 'NAZWA', $this->NAZWA])
            ->andFilterWhere(['like', 'SYM', $this->SYM])
            ->andFilterWhere(['like', 'SYMPOD', $this->SYMPOD]);

        return $dataProvider;
    }
}
