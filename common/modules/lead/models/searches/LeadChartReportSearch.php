<?php

namespace common\modules\lead\models\searches;

use common\helpers\ArrayHelper;
use common\models\user\User;
use common\modules\lead\chart\LeadStatusColor;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadUser;
use common\modules\lead\models\query\LeadReportQuery;
use DateTime;
use Yii;

class LeadChartReportSearch extends LeadReportSearch {

	public ?LeadStatusColor $statusColor = null;

	public bool $groupLeadStatus = false;

	public function getLeadStatusColor(): LeadStatusColor {
		if ($this->statusColor === null) {
			$this->statusColor = new LeadStatusColor();
		}
		return $this->statusColor;
	}

	public function rules(): array {
		return array_merge([
			['from_at', 'default', 'value' => $this->getDefaultFromAt()],
			['to_at', 'default', 'value' => $this->getDefaultToAt()],
			['groupLeadStatus', 'boolean'],
		], parent::rules());
	}

	public function attributeLabels(): array {
		return array_merge(parent::attributeLabels(), [
			'groupLeadStatus' => Yii::t('lead', 'Group lead status'),
		]);
	}

	public function getDefaultFromAt(): string {
		$currentDate = new DateTime();
		$currentDate->modify('Monday this week');
		return $currentDate->format('Y-m-d');
	}

	public function getDefaultToAt(): string {
		$currentDate = new DateTime();
		$currentDate->modify('Sunday this week');
		return $currentDate->format('Y-m-d');
	}

	public function getBaseQuery(bool $validate = false): LeadReportQuery {
		$query = LeadReport::find();
		if ($validate && !$this->validate()) {
			$query->andWhere('0=1');
		}
		$this->applyLeadReportFilter($query);

		return $query;
	}

	public function getOwnersNames(): array {
		$query = LeadReport::find();
		$this->applyDateFilter($query);
		return User::getSelectList(
			$query
				->select('owner_id')
				->distinct()
				->column(),
			false);
	}

	public function getLeadTypesCount(): array {
		$query = $this->getBaseQuery();
		$query->joinWith('lead.leadSource', false);
		$query->groupBy(
			[
				LeadSource::tableName() . '.type_id',
			]);
		$query->select([
			LeadSource::tableName() . '.type_id',
			'count(*) as count',
		]);
		$data = $query->asArray()->all();
		$data = ArrayHelper::map($data, 'type_id', 'count');
		$data = array_map('intval', $data);
		arsort($data);
		return $data;
	}

	public function getLeadUserTimeStats(): array {
		$query = LeadUser::find()
			->alias('base')
			->andWhere([
				'base.lead_id' => $this->getBaseQuery()
					->select(LeadReport::tableName() . '.lead_id'),
				'base.user_id' => array_keys($this->getOwnersNames()),
			]);

		/**
		 * @var LeadUser[] $models
		 */
		$models = $query->all();

		$data = [];
		foreach ($models as $model) {

			$first = $model->getFirstViewDuration();
			if ($first > 0) {
				if (!isset($data[$model->user_id])) {
					$data[$model->user_id] = [];
				}
				$data[$model->user_id]['first'][] = (int) $first;
			}
		}
		foreach ($data as &$row) {
//			echo Html::dump($row);
			$values = $row['first'];
			$row['min'] = min($values);
			$row['max'] = max($values);
		}
		return $data;
	}

	protected function applyLeadReportFilter(LeadReportQuery $query) {
		$this->applyDateFilter($query);
		$this->applyStatusesFilter($query);
		$this->applyUserFilter($query);
		$this->applyLeadTypeFilter($query);
		$this->applyLeadStatusFilter($query);
	}

}
