<?php

namespace backend\modules\settlement\models\search;

use common\models\issue\IssuePayCalculation;
use common\models\settlement\PayReceived;
use common\models\user\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * PayReceivedSearch represents the model behind the search form of `common\models\settlement\PayReceived`.
 */
class PayReceivedSearch extends PayReceived {

	public $calculationType;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['pay_id', 'user_id', 'calculationType'], 'integer'],
			[['date_at', 'transfer_at'], 'safe'],
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
	public function search(array $params): ActiveDataProvider {

		$query = PayReceived::find();
		$query->joinWith('user');
		$query->joinWith('pay');
		$query->joinWith('pay.calculation C');

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
			'user_id' => $this->user_id,
			'date_at' => $this->date_at,
			'transfer_at' => $this->transfer_at,
			'C.type' => $this->calculationType,
		]);

		return $dataProvider;
	}

	public static function getUserNames(): array {
		return User::getSelectList(PayReceived::find()->select('user_id')->distinct()->column());
	}

	public static function getCalculationTypesNames(): array {
		return IssuePayCalculation::getTypesNames();
	}
}
