<?php

namespace common\modules\lead\components;

use common\modules\lead\models\CampaignCost;
use common\modules\lead\models\Lead;
use yii\base\Component;

class CostComponent extends Component {

	public function recalculateFromDate(
		string $fromAt,
		string $toAt,
		array $campaignsIds = []) {

		$data = $this->getCostData($fromAt, $toAt, $campaignsIds);
		foreach ($data as $model) {
			if (!empty($model->leads_ids)) {
				Lead::updateAll([
					'cost_value' => $model->single_cost_value,
				], [
					'id' => $model->leads_ids,
				]);
			}
		}

		return $data;
	}

	/**
	 * @param string|null $fromAt
	 * @param string|null $toAt
	 * @param array $campaignsIds
	 * @return CampaignCost[]
	 */
	public function getCostData(
		?string $fromAt,
		?string $toAt,
		array $campaignsIds = []
	): array {
		return CampaignCost::getModels($fromAt, $toAt, $campaignsIds);
	}

}
