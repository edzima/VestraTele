<?php
namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
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
	 
	public $finished;
	public $status;
	public $tele;
	 
    public function rules()
    {
        return [
            [['id', 'task_id', 'connexion', 'finished','status'], 'integer'],
            [['score'], 'number'],
            [['date', 'name','tele'], 'safe'],
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
		$query->joinWith(['score','taskstatus','tele'])->where('count_agreement>0');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
		
				
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
	

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'task_id' => $this->task_id,
            'connexion' => $this->connexion,
            'score' => $this->score,
            'date' => $this->date,
			'task_status.finished' =>$this->finished,
			'task_status.point' =>$this->status,
        ]);

		
        $query->andFilterWhere(['like', 'name', $this->name])
			->andFilterWhere(['like', 'user.username', $this->tele]);

        return $dataProvider;
    }
}
