<?php

namespace backend\models\search;

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
	 public $accident;
	 public $wojewodztwo;
	 public $powiatRel;
	 public $gminaRel;
	 public $agent;

    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at', 'meeting', 'finish', 'taskstatus','automat','agent',], 'integer'],
            [['victim_name', 'phone', 'qualified_name', 'details', 'miasto','tele', 'answer', 'accident','date','wojewodztwo', 'powiatRel', 'gminaRel'], 'safe'],

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
    public function search($params, $key)
    {
		$typWork= Yii::$app->user->identity->typ_work;
        $query = Task::find();

		//selected rows
		if($key)$query->where('task.id IN('.$key.')');

		//typ_work => A => admin || manager, all records
	    $query->joinWith(['miasto','tele','taskstatus','taskstatus.answer','accident','wojewodztwo','powiatRel', 'gminaRel']);


		//$query->joinWith(['miasto','tele','taskstatus','taskstatus.answer']);
		//$query->where(['task.agent_id' => Yii::$app->user->identity->id]);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			 'pagination' => [
				'pageSize' => 40,
			],
        ]);

        $this->load($params);

     $dataProvider->sort = ['defaultOrder' => ['id' => 'ASC']]; 
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

		$dataProvider->sort->attributes['accident'] = [
			// The tables are the ones our relation are configured to
			// in my case they are prefixed with "tbl_"
			'asc' => ['accident_typ.name' => SORT_ASC],
			'desc' => ['accident_typ.name' => SORT_DESC],
		];


        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');

            return $dataProvider;
        }

		//is raport task-status
		if(strlen($this->taskstatus)==1){
			if($this->taskstatus) $query->andFilterWhere(['>', 'task_status.answer_id',0]);
			else $query->andWhere(['task_id' => null]);
		}

        // grid filtering conditions
        $query->andFilterWhere([
            'task.id' => $this->id,
			'finished' => $this->finish,
            'tele_id' => $this->tele,
			'agent_id' => $this->agent,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'accident_id' => $this->accident_id,
            'wojewodztwa.id' => $this->wojewodztwo,
            'meeting' => $this->meeting,
			'answer_typ.id'=> $this->answer,
			'accident_typ.id'=> $this->accident,
			'automat'=>$this->automat

        ]);


        $query->andFilterWhere(['like', 'victim_name', $this->victim_name])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'qualified_name', $this->qualified_name])
            ->andFilterWhere(['like', 'details', $this->details])
			->andFilterWhere(['like', 'miasta.name', $this->miasto])
			->andFilterWhere(['like', 'powiaty.name', $this->powiatRel])
			->andFilterWhere(['like', 'terc.name', $this->gminaRel])
			->andFilterWhere(['like', 'date', $this->date]);



        return $dataProvider;
    }
}
