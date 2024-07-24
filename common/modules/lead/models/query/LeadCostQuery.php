<?php

namespace common\modules\lead\models\query;

use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadCost;
use yii\db\ActiveQuery;
use yii\db\Expression;

class LeadCostQuery extends ActiveQuery {

	/**
	 * @inheritdoc
	 * @return LeadCost[]|array
	 */
	public function all($db = null) {
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return LeadCost|array|null
	 */
	public function one($db = null) {
		return parent::one($db);
	}

	public function withLeadsCount(bool $eagerLoading = false) {
		$this->joinWith('leads', $eagerLoading)
			->addSelect([
				LeadCost::tableName() . '.*',
				new Expression('COUNT(' . Lead::tableName() . '.id) as leads_count'),
				new Expression(LeadCost::tableName() . '.value / ' . 'count( ' . Lead::tableName() . '.id) as single_lead_cost_value'),
				new Expression('GROUP_CONCAT(' . Lead::tableName() . '.id) AS leads_ids'),
			])
			->groupBy([LeadCost::tableName() . '.id']);
		return $this;
	}

	public function onlyWithLeads(bool $eagerLoading = true) {
		$this->joinWith('leads', $eagerLoading);
		$this->andWhere(['not', [Lead::tableName() . '.id' => null]]);
		return $this;
	}

	public function onlyLeadsWithoutCostValue(bool $eagerLoading = true) {
		$this->joinWith('leads', $eagerLoading);
		$this->andWhere([Lead::tableName() . '.cost_value' => null]);
		return $this;
	}

	public function withoutLeads(bool $eagerLoading = true) {
		$this->joinWith('leads');
		$this->andWhere([Lead::tableName() . '.id' => null]);
		return $this;
	}

}
