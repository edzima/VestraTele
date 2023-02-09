<?php

namespace common\models\issue\search;

use common\helpers\ArrayHelper;
use common\models\issue\Summon;
use common\models\issue\SummonDocLink;
use common\models\user\CustomerSearchInterface;
use common\validators\PhoneValidator;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\QueryInterface;

class SummonDocLinkSearch extends SummonDocLink implements CustomerSearchInterface {

	public string $docName = '';
	public string $customerName = '';
	public string $customerPhone = '';
	public $issue_id;

	public $summonTypeId;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['doc_type_id', 'summon_id', 'issue_id', 'summonTypeId'], 'integer'],
			['docName', 'string', 'min' => CustomerSearchInterface::MIN_LENGTH],
			['customerName', 'string', 'min' => CustomerSearchInterface::MIN_LENGTH],
			['customerPhone', PhoneValidator::class],
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
	 * @return ActiveDataProvider
	 */
	public function search(array $params): ActiveDataProvider {
		$query = SummonDocLink::find();

		$query->with([
			'doc',
			'summon',
			'summon.type',
			'summon.issue',
			'summon.issue.customer.userProfile',
		]);

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

		$this->applySummonTypeFilter($query);
		$this->applyCustomerNameFilter($query);
		$this->applyDocFilter($query);

		return $dataProvider;
	}

	public function getDocsNames(): array {
		//@todo add user filter
		return ArrayHelper::map(SummonDocLink::find()
			->select(['doc_type_id', 'name'])
			->joinWith('doc')
			->distinct()
			->asArray()
			->all(), 'doc_type_id', 'name');
	}

	public function getSummonTypesNames(): array {
		//@todo add user filter

		return ArrayHelper::map(SummonDocLink::find()
			->select(['type_id', 'name'])
			->joinWith('summon.type')
			->distinct()
			->asArray()
			->all(), 'type_id', 'name');
	}

	private function applySummonTypeFilter(ActiveQuery $query) {
		if (!empty($this->summonTypeId)) {
			$query->joinWith('summon');
			$query->andWhere([
				Summon::tableName() . '.type_id' => $this->summonTypeId,
			]);
		}
	}

	private function applyDocFilter(ActiveQuery $query) {
		if (!empty($this->doc_type_id)) {
			$query->andWhere([
				SummonDocLink::tableName() . '.doc_type_id' => $this->doc_type_id,
			]);
		}
	}

	private function applyCustomerFilter(ActiveQuery $query) {
		if (!empty($this->customerLastname)) {
			$query->andWhere([
				SummonDocLink::tableName() . '.doc_type_id' => $this->doc_type_id,
			]);
		}
	}

	public function applyCustomerNameFilter(QueryInterface $query): void {
		if (!empty($this->customerName)) {
			$query->joinWith([
				'summon.issue.customer.userProfile CP' => function (ActiveQuery $query) {
					$query->andWhere([
						'like',
						new Expression("CONCAT(CP.lastname,' ', CP.firstname)"),
						$this->customerName . '%', false,
					]);
					$query->orWhere([
						'like',
						new Expression("CONCAT(CP.firstname,' ', CP.lastname)"),
						$this->customerName . '%', false,
					]);
				},
			]);
		}
	}
}
