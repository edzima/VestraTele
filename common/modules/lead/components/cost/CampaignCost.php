<?php

namespace common\modules\lead\components\cost;

use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadCost;
use yii\base\Model;
use yii\db\Query;

class CampaignCost extends Model {

	public ?string $fromAt = null;
	public ?string $toAt = null;

	public int $campaign_id;

	public ?float $sum;
	public ?float $single_cost_value;
	public array $leads_ids = [];

	public static function getModels(
		?string $fromAt,
		?string $toAt,
		array $campaignsIds = []) {

		$subQuery = LeadCost::find()
			->between($fromAt, $toAt)
			->andFilterWhere([
				LeadCost::tableName() . '.campaign_id' => $campaignsIds,
			]);

		$subQuery->select([
			'sum(value) as sum',
			'campaign_id',
		]);
		$subQuery->groupBy([
			LeadCost::tableName() . '.campaign_id',
		]);
		$query = new Query();
		$query->select([
			'c.sum',
			'c.campaign_id',
			'count(l.id) as leads_count',
			'c.sum/count(l.id) as single_cost_value',
			'GROUP_CONCAT(l.id) as leads_ids',
		]);
		$query->from(['c' => $subQuery]);
		$query->leftJoin(['l' => Lead::tableName()], 'l.campaign_id = c.campaign_id');
		$query->andWhere([
			'between',
			'l.date_at',
			date('Y-m-d 00:00:00', strtotime($fromAt)),
			date('Y-m-d 23:59:59', strtotime($toAt)),
		]);
		$query->groupBy('c.campaign_id');
		$data = $query->all();
		$models = [];
		foreach ($data as $datum) {
			$model = new static([
				'fromAt' => $fromAt,
				'toAt' => $toAt,
				'sum' => $datum['sum'],
				'campaign_id' => $datum['campaign_id'],
				'single_cost_value' => $datum['single_cost_value'],
				'leads_ids' => $datum['leads_ids']
					? explode(',', $datum['leads_ids'])
					: [],
			]);
			$models[] = $model;
		}
		return $models;
	}

}
