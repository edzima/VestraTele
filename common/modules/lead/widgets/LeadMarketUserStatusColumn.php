<?php

namespace common\modules\lead\widgets;

use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadMarketUser;
use common\modules\lead\models\searches\LeadMarketSearch;
use common\widgets\grid\DataColumn;
use Yii;

class LeadMarketUserStatusColumn extends DataColumn {

	public $attribute = 'userStatus';

	public function init() {
		parent::init();
		if ($this->filter === null && $this->grid->filterModel instanceof LeadMarketSearch) {
			$this->filter = $this->grid->filterModel::getMarketUserStatusesNames();
		}
		if ($this->label === null) {
			$this->label = Yii::t('lead', 'Market Users Count');
		}
		if ($this->value === null) {
			$this->value = function (LeadMarket $data): ?string {
				return $this->renderUserStatusesCount($data);
			};
		}
	}

	public function renderUserStatusesCount(LeadMarket $data): ?string {
		$users = $data->leadMarketUsers;
		if (empty($users)) {
			return 0;
		}
		$statuses = [];
		foreach ($users as $marketUser) {
			if (!isset($statuses[$marketUser->status])) {
				$statuses[$marketUser->status] = 1;
			} else {
				$statuses[$marketUser->status]++;
			}
		}
		$content = [];
		foreach ($statuses as $status => $count) {
			$content[] = LeadMarketUser::getStatusesNames()[$status] . ': ' . $count;
		}
		return implode("\n", $content);
	}
}
