<?php

namespace common\modules\lead\models\searches;

use common\modules\lead\models\LeadQuestion;
use common\modules\lead\models\LeadStatus;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\lead\models\LeadAnswer;
use yii\helpers\ArrayHelper;

/**
 * LeadAnswerSearch represents the model behind the search form of `common\modules\lead\models\LeadAnswer`.
 */
class LeadAnswerSearch extends LeadAnswer {

	public $old_status_id;
	public $status_id;

	public static function getStatusNames(): array {
		return LeadStatus::getNames();
	}

	public static function getQuestionsNames(): array {
		return ArrayHelper::map(LeadQuestion::find()
			->andWhere(['id' => LeadAnswer::find()->select('question_id')])
			->asArray()
			->all(), 'id', 'name');
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['report_id', 'question_id', 'old_status_id', 'status_id'], 'integer'],
			[['answer'], 'safe'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function scenarios(): array {
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
	public function search(array $params) {
		$query = LeadAnswer::find();
		$query->joinWith('report R');

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
			'report_id' => $this->report_id,
			'question_id' => $this->question_id,
			'R.status_id' => $this->status_id,
			'R.old_status_id' => $this->old_status_id,
		]);

		$query->andFilterWhere(['like', 'answer', $this->answer]);

		return $dataProvider;
	}
}
