<?php

namespace common\modules\lead\components;

use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadCost;
use yii\base\Component;

class CostComponent extends Component {

	public function recalculate(int $campaign_id, string $date_at): ?int {
		/** @var LeadCost|null $model */
		$model = LeadCost::find()
			->andWhere(['campaign_id' => $campaign_id])
			->andWhere(['date_at' => $date_at])
			->one();
		if (empty($model)) {
			return null;
		}
		$leadsIds = $model->getLeads()->select([Lead::tableName() . '.id'])->column();
		if (empty($leadsIds)) {
			return null;
		}
		$value = $model->value / count($leadsIds);
		return $this->updateLeadCosts($value, $leadsIds);
	}

	public function updateLeadCosts(?float $value, array $ids): int {
		return Lead::updateAll(['cost_value' => $value], ['id' => $ids]);
	}

	public function recalculateAllMissing() {
		$models = LeadCost::find()
			->withLeadsCount()
			->onlyWithLeads()
			->onlyLeadsWithoutCostValue()
			->all();

		$count = 0;
		foreach ($models as $model) {
			$count += $this->updateLeadCosts($model->getSingleLeadCostValue(), $model->getLeadsIds());
		}
		return $count;
	}
}
