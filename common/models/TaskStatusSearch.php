<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Task;

/**
 * TaskSearch represents the model behind the search form of `common\models\Task`.
 */
class TaskStatusSearch extends Task
{
    /**
     * @inheritdoc
     */
	 public $miasto;
	 public $tele;
	 public $taskstatus;
	 public $finish;
	 public $answer;
	 
    public function rules()
    {
        return [
            [['id', 'tele_id', 'agent_id', 'created_at', 'updated_at', 'accident_id', 'woj', 'powiat', 'gmina', 'city', 'meeting', 'date', 'finish', 'taskstatus'], 'integer'],
            [['victim_name', 'phone', 'qualified_name', 'details', 'miasto','tele', 'answer'], 'safe'],
			
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
		$typWork= Yii::$app->user->identity->typ_work;
		if($typWork=='T') $user = 'tele_id';
		if($typWork=='P') $user = 'agent_id';
        $query = Task::find();

		var_dump($typWork);
		$query->joinWith(['miasto','tele','taskstatus','taskstatus.answer'])->where([$user=>Yii::$app->user->identity->id]);

        // add conditions that should always apply here
		
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
		
        $this->load($params);
		// Important: here is how we set up the sorting
		// The key is the attribute name on our "TourSearch" instance
		$dataProvider->sort->attributes['miasto'] = [
			// The tables are the ones our relation are configured to
			// in my case they are prefixed with "tbl_"
			'asc' => ['miasta.name' => SORT_ASC],
			'desc' => ['miasta.name' => SORT_DESC],
		];
		
		$dataProvider->sort->attributes['tele'] = [
			// The tables are the ones our relation are configured to
			// in my case they are prefixed with "tbl_"
			'asc' => ['user.username' => SORT_ASC],
			'desc' => ['user.username' => SORT_DESC],
		];
		

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
	
            return $dataProvider;
        }
		
		//is raport task-status
		
		if(strlen($this->taskstatus)==1){
			if($this->taskstatus==0) $query->where('task_status.created_at is null');
			else $query->where('task_status.created_at is not null');
		} 

        // grid filtering conditions
        $query->andFilterWhere([
            'task.id' => $this->id,
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
			'task_status.finished'=> $this->finish,
			'answer_typ.id'=> $this->answer
			
        ]);
		
		
        $query->andFilterWhere(['like', 'victim_name', $this->victim_name])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'qualified_name', $this->qualified_name])
            ->andFilterWhere(['like', 'details', $this->details])
			->andFilterWhere(['like', 'miasta.name', $this->miasto])
			->andFilterWhere(['like', 'user.username', $this->tele]);
			


        return $dataProvider;
    }
}
