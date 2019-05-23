<?php

namespace common\models\issue;

use common\models\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * IssueSearch represents the model behind the search form of `common\models\issue\Issue`.
 */
class IssueSearch extends Issue {

	public $clientCity;
	public $clientState;

	public $payStatus;
	public $createdAtFrom;
	public $createdAtTo;
	public $childsId;

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[
				[
					'id', 'agent_id', 'tele_id', 'lawyer_id', 'childsId', 'client_city_id', 'client_street',
					'victim_city_id', 'provision_type', 'stage_id', 'type_id', 'entity_responsible_id',
				], 'integer',
			],
			[
				['payed'], 'boolean',
			],
			[['createdAtTo', 'createdAtFrom'], 'date', 'format' => DATE_ATOM],
			[
				[
					'created_at', 'updated_at', 'client_first_name', 'client_surname', 'client_phone_1',
					'client_phone_2', 'client_city_code', 'victim_first_name', 'victim_surname', 'victim_city_code',
					'victim_street', 'victim_phone', 'details',
				], 'safe',
			],
			[['clientCity', 'clientState'], 'safe'],
			[['clientCity', 'clientState'], 'default', 'value' => null],
			['payStatus', 'integer'],
			['payStatus', 'in', 'range' => array_keys(static::payStatuses())],
		];
	}

	public function attributeLabels() {
		return array_merge([
			'createdAtFrom' => 'Dodano od',
			'createdAtTo' => 'Dodano do',
			'childsId' => 'Struktury',
		], parent::attributeLabels());
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios() {
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
	public function search($params) {
		$query = Issue::find();

		$query->with(['pays', 'agent.userProfile', 'type', 'stage.types']);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => [
					'updated_at' => SORT_DESC,
				],
			],
		]);

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		if ($this->clientState !== null) {
			$query->joinWith('clientState');
			$query->andFilterWhere(['wojewodztwa.id' => $this->clientState]);
		}

		if ($this->clientCity !== null) {
			$query->joinWith('clientCity');
			$query->andFilterWhere(['like', 'miasta.name', $this->clientCity]);
		}

		if ($this->childsId > 0) {
			$user = User::findOne($this->childsId);
			if ($user !== null) {
				$ids = $user->getAllChildsIds();
				$ids[] = $user->id;
				$query->andFilterWhere(['agent_id' => $ids]);
			}
		}

		$this->payedFilter($query);
		$this->teleFilter($query);
		$this->lawyerFilter($query);

		// grid filtering conditions
		$query->andFilterWhere([
			'id' => $this->id,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'agent_id' => $this->agent_id,
			'client_street' => $this->client_street,
			'victim_city_id' => $this->victim_city_id,
			'provision_type' => $this->provision_type,
			'provision_value' => $this->provision_value,
			'provision_base' => $this->provision_base,
			'stage_id' => $this->stage_id,
			'type_id' => $this->type_id,
			'entity_responsible_id' => $this->entity_responsible_id,
			'payed' => $this->payed,
		]);

		$query->andFilterWhere(['like', 'client_first_name', $this->client_first_name])
			->andFilterWhere(['like', 'client_surname', $this->client_surname])
			->andFilterWhere(['like', 'client_phone_1', $this->client_phone_1])
			->andFilterWhere(['like', 'client_phone_2', $this->client_phone_2])
			->andFilterWhere(['like', 'client_city_code', $this->client_city_code])
			->andFilterWhere(['like', 'victim_first_name', $this->victim_first_name])
			->andFilterWhere(['like', 'victim_surname', $this->victim_surname])
			->andFilterWhere(['like', 'victim_city_code', $this->victim_city_code])
			->andFilterWhere(['like', 'victim_street', $this->victim_street])
			->andFilterWhere(['like', 'victim_phone', $this->victim_phone])
			->andFilterWhere(['>=', 'created_at', $this->createdAtFrom])
			->andFilterWhere(['<=', 'created_at', $this->createdAtTo])
			->andFilterWhere(['like', 'details', $this->details]);

		return $dataProvider;
	}

	protected function teleFilter(IssueQuery $query): void {
		$query->andFilterWhere(['tele_id' => $this->tele_id]);
	}

	protected function lawyerFilter(IssueQuery $query): void {
		$query->andFilterWhere(['lawyer_id' => $this->lawyer_id]);
	}

	private function payedFilter(IssueQuery $query): void {

		if ($this->payStatus !== null) {
			switch ($this->payStatus) {
				case static::PAYED_ALL:
					$query->onlyPayed();
					break;
				case static::PAYED_PART:
					$query->onlyPartPay();
					break;
				case static::PAYED_NOT:
					$query->onlyWithoutPay();
					break;
			}
		}
	}

	public static function getTypesNames(): array {
		return ArrayHelper::map(IssueType::find()->all(), 'id', 'nameWithShort');
	}

	private static $stages;

	public static function getStagesNames(): array {
		if (static::$stages === null) {
			static::$stages = ArrayHelper::map(IssueStage::find()->all(), 'id', 'nameWithShort');
		}
		return static::$stages;
	}
}
