<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use common\models\Score;

use common\models\Task;

/**
 * ScoreSearch represents the model behind the search form of `common\models\Score`.
 */
class ScoreSearch extends Score
{
    /**
     * @inheritdoc
     */
	 
	public $suma; 
	public $start_at;
	public $finish_at;

    public function rules()
    {
      return [
	    [['start_at', 'finish_at'], 'safe'],
	    [['start_at', 'finish_at'], 'date','format' => 'yyyy-M-d'],
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
        $query = Score::find()->groupBy('tele_id')->select('name, tele_id, sum(score) as suma')->with('tele')->orderBy('suma DESC');
	
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
	
		
			if(strlen($this->start_at)&&strlen($this->finish_at))$query->where("date BETWEEN '$this->start_at' AND '$this->finish_at'");
			else if(strlen($this->start_at))$query->where("date > '$this->start_at'");
			else if(strlen($this->finish_at))$query->where("date < '$this->finish_at'");
		
		
        return $dataProvider;
    }
}
