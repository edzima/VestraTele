<?php

namespace common\models\issue;

use common\models\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * IssueSearch represents the model behind the search form of `common\models\issue\Issue`.
 */
class IssueSearch extends Issue {

	public $clientCity;
	public $clientState;

	public $createdAtFrom;
	public $createdAtTo;
	public $childsId;
	public $disabledStages = [];
	public $onlyDelayed = false;

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[
				[
					'id', 'agent_id', 'tele_id', 'lawyer_id', 'childsId', 'client_city_id', 'client_street',
					'victim_city_id', 'provision_type', 'stage_id', 'type_id', 'entity_responsible_id',
				], 'integer',
			],
			[
				['payed', 'onlyDelayed'], 'boolean',
			],
			[['createdAtTo', 'createdAtFrom', 'accident_at'], 'date', 'format' => DATE_ATOM],
			[
				[
					'created_at', 'updated_at', 'client_first_name', 'client_surname', 'client_phone_1',
					'client_phone_2', 'client_city_code', 'victim_first_name', 'victim_surname', 'victim_city_code',
					'victim_street', 'victim_phone', 'details', 'disabledStages',
				], 'safe',
			],
			[['clientCity', 'clientState'], 'safe'],
			[['clientCity', 'clientState'], 'default', 'value' => null],
		];
	}

	public function attributeLabels(): array {
		return array_merge([
			'createdAtFrom' => 'Dodano od',
			'createdAtTo' => 'Dodano do',
			'childsId' => 'Struktury',
			'disabledStages' => 'Wykluczone etapy',
			'stage_change_at' => 'Zmiana etapu',
			'onlyDelayed' => 'Opóźnione',
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

		$query->with(['agent.userProfile', 'type', 'stage.types']);

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
				$ids = $user->getAllChildesIds();
				$ids[] = $user->id;
				$query->andFilterWhere(['agent_id' => $ids]);
			}
		}

		$this->teleFilter($query);
		$this->lawyerFilter($query);
		$this->delayedFilter($query);

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
			'accident_at' => $this->accident_at,
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
			->andFilterWhere(['NOT IN', 'stage_id', $this->disabledStages])
			->andFilterWhere(['like', 'details', $this->details]);

		return $dataProvider;
	}

	protected function teleFilter(IssueQuery $query): void {
		$query->andFilterWhere(['tele_id' => $this->tele_id]);
	}

	protected function lawyerFilter(IssueQuery $query): void {
		$query->andFilterWhere(['lawyer_id' => $this->lawyer_id]);
	}

	private function delayedFilter(IssueQuery $query): void {
		if (!empty($this->onlyDelayed)) {
			$query->joinWith('stage');
			$daysGroups = ArrayHelper::map(static::getStages(), 'id', 'days_reminder', 'days_reminder');

			foreach ($daysGroups as $day => $ids) {
				if (!empty($day)) {
					$query->orFilterWhere([
						'and',
						[
							'stage_id' => array_keys($ids),
						],
						[
							'<=', new Expression("DATE_ADD(stage_change_at, INTERVAL $day DAY)"), new Expression('NOW()'),
						],
					]);
				}
			}
			$query->andWhere('stage_change_at IS NOT NULL');
			$query->andWhere('issue_stage.days_reminder is NOT NULL');
		}
	}

	public static function getTypesNames(): array {
		return ArrayHelper::map(IssueType::find()->all(), 'id', 'nameWithShort');
	}

	private static $stages;

	public static function getStagesNames(): array {
		return ArrayHelper::map(static::getStages(), 'id', 'nameWithShort');
	}

	private static function getStages(): array {
		if (static::$stages === null) {
			static::$stages = IssueStage::find()->all();
		}
		return static::$stages;
	}
}
