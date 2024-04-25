<?php

namespace common\modules\lead\models\searches;

use common\helpers\ArrayHelper;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadUser;
use common\modules\lead\models\query\LeadQuery;
use yii\data\ActiveDataProvider;

class LeadChartSearch extends LeadSearch {

	public string $defaultStartDateFormat = 'Y-m-01';
	public string $defaultEndDateFormat = 'Y-m-t 23:59:59';

	public function getUniqueId(): string {
		return md5(serialize($this->toArray()));
	}

	public function rules(): array {
		return array_merge([
			['from_at', 'default', 'value' => date($this->defaultStartDateFormat)],
			['to_at', 'default', 'value' => date($this->defaultEndDateFormat)],
		], parent::rules());
	}

	public function search(array $params = []): ActiveDataProvider {
		$query = Lead::find();
		$this->load($params);
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
		if (!$this->validate()) {
			$query->andWhere('0=1');
			return $dataProvider;
		}
		$this->applyDateFilter($query);

		return $dataProvider;
	}

	public function getLeadTypesCount(): array {
		$query = $this->getBaseQuery();
		$query->joinWith('leadSource');
		$query->groupBy(LeadSource::tableName() . '.type_id');
		$query->select([LeadSource::tableName() . '.type_id', 'count(*) as count']);
		$query->asArray();
		$data = $query->all();
		$data = ArrayHelper::map($data, 'type_id', 'count');
		$data = array_map('intval', $data);
		arsort($data);
		return $data;
	}

	public function getLeadStatusesCount(): array {
		$query = $this->getBaseQuery();
		$query->groupBy(Lead::tableName() . '.status_id');
		$query->select([Lead::tableName() . '.status_id', 'count(*) as count']);
		$query->asArray();
		$data = $query->all();
		$data = ArrayHelper::map($data, 'status_id', 'count');
		$data = array_map('intval', $data);
		arsort($data);
		return $data;
	}

	public function getLeadsUsersCount(): array {
//		$query = LeadUser::find();
//		$query->joinWith([
//			'lead' => function (LeadQuery $query) {
//				$this->applyDateFilter($query);
//			},
//		]);
		$query = $this->getBaseQuery();
		$query->joinWith('leadUsers');
		$query->addSelect(['*', 'count(*) as count']);
		$query->groupBy([
			LeadUser::tableName() . '.user_id',
			//	LeadUser::tableName() . '.lead_id',
		]);
		$query->distinct();
		$query->asArray();
		$data = $query->all();
		$data = ArrayHelper::map($data, 'user_id', 'count');
		$data = array_map('intval', $data);
		arsort($data);
		return $data;
	}

	protected function getBaseQuery(bool $validate = false): LeadQuery {
		$query = Lead::find();
		if ($validate && !$this->validate()) {
			$query->andWhere('0=1');
		}
		$this->applyLeadFilter($query);

		return $query;
	}

	protected function applyLeadFilter(LeadQuery $query): void {
		$this->applyDateFilter($query);
		$this->applyExcludedStatusFilter($query);
		$this->applyUserFilter($query);
	}

	public function getLeadsByDays(): array {
		$query = $this->getBaseQuery();
		$query->groupBy(['EXTRACT(day FROM date_at)', 'status_id']);
		$query->select([
			'count(*) AS count',
			'DATE(date_at) as date',
			'status_id',
		]);
		$query->orderBy(['date_at' => SORT_ASC]);
		$query->asArray();
		return $query->all();
	}

}
