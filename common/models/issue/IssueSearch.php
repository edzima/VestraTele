<?php

namespace common\models\issue;

use common\models\issue\query\IssueQuery;
use common\models\user\Worker;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * IssueSearch represents the model behind the search form of `common\models\issue\Issue`.
 */
class IssueSearch extends Issue {

	public $createdAtFrom;
	public $createdAtTo;
	public $childsId;
	public $disabledStages = [];
	public $onlyDelayed = false;
	public $withArchive = false;

	private $stages = [];

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[
				[
					'id', 'agent_id', 'tele_id', 'lawyer_id', 'childsId', 'provision_type', 'stage_id', 'type_id', 'entity_responsible_id',
				], 'integer',
			],
			[
				['payed', 'onlyDelayed'], 'boolean',
			],
			[['createdAtTo', 'createdAtFrom', 'accident_at'], 'date', 'format' => DATE_ATOM],
			['stage_id', 'in', 'range' => array_keys($this->getStagesNames())],
			[
				[
					'created_at', 'updated_at', 'client_first_name', 'client_surname', 'victim_first_name', 'victim_surname', 'victim_city_code',
					'victim_street', 'details', 'disabledStages',
				], 'safe',
			],
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

		if ($this->childsId > 0) {
			$user = Worker::findOne($this->childsId);
			if ($user !== null) {
				$ids = $user->getAllChildesIds();
				$ids[] = $user->id;
				$query->andFilterWhere(['agent_id' => $ids]);
			}
		}

		$this->teleFilter($query);
		$this->lawyerFilter($query);
		$this->delayedFilter($query);
		$this->archiveFilter($query);

		// grid filtering conditions
		$query->andFilterWhere([
			'id' => $this->id,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'agent_id' => $this->agent_id,
			'client_street' => $this->client_street,
			'client_city_id' => $this->client_city_id,
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

	protected function archiveFilter(IssueQuery $query): void {
		if (!$this->withArchive) {
			$query->withoutArchives();
		}
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
			$daysGroups = ArrayHelper::map($this->getStagesNames(), 'id', 'days_reminder', 'days_reminder');

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

	public function getStagesNames(): array {
		if (empty($this->stages)) {
			$this->stages = ArrayHelper::map(IssueStage::find()->all(), 'id', 'nameWithShort');
		}
		if (!$this->withArchive && isset($this->stages[IssueStage::ARCHIVES_ID])) {
			unset($this->stages[IssueStage::ARCHIVES_ID]);
		}
		return $this->stages;
	}
}
