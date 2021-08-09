<?php

namespace common\modules\calendar\models\searches;

use common\models\SearchModel;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\searches\LeadReminderSearch;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;

class LeadCalendarSearch extends LeadReminderSearch implements FilterSearch, SearchModel {

	const SCENARIO_DEFAULT = self::SCENARIO_USER;

	public ?array $filters = null;

	public ?bool $onlyDelayed = false;

	public function search(array $params = []): ActiveDataProvider {
		if (empty($this->user_id)) {
			throw new InvalidConfigException('$user_id must be set.');
		}
		$dataProvider = parent::search($params);
		$dataProvider->setPagination(false);
		return $dataProvider;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFilters(): array {
		if ($this->filters === null) {
			$statuses = LeadStatus::find()
				->andWhere('calendar IS NOT NULL')
				->all();
			$filters = [];
			foreach ($statuses as $status) {
				$filters[$status->id] = $status->getFilter();
			}
			$this->filters = $filters;
		}
		return $this->filters;
	}

}
