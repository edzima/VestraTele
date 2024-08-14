<?php

namespace common\modules\lead\models\searches;

use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadCampaign;
use common\modules\lead\models\LeadCost;
use common\modules\lead\models\query\LeadCostQuery;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class LeadCampaignCostSearch extends Model {

	public array $campaignIds = [];

	public ?string $fromAt;
	public $toAt;

	public $userId;

	public bool $withChildesCampaigns = true;

	public const SCENARIO_USER = 'user';

	private ?float $costSum = null;

	public function rules(): array {
		return [
			['campaignIds', 'required'],
			['!userId', 'required', 'on' => static::SCENARIO_USER],
			[['fromAt', 'toAt'], 'safe'],
		];
	}

	public function attributeLabels(): array {
		return [
			'fromAt' => Yii::t('lead', 'From At'),
			'toAt' => Yii::t('lead', 'To At'),
			'costSum' => Yii::t('lead', 'Cost'),
		];
	}

	public function getCostSum(): ?float {
		if (empty($this->costSum)) {
			$this->costSum = $this->getCostQuery()->sum('value');
		}
		return $this->costSum;
	}

	public function getCostQueryDataProvider(): ActiveDataProvider {
		$query = $this->getCostQuery();
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => [
					'date_at' => SORT_DESC,
				],
			],
		]);

		if (!$this->validate()) {
			$query->where('0=1');
		}
		return $dataProvider;
	}

	public function getCostData(): array {
		$subQuery = $this->getCostQuery();
		$subQuery->select([
			'sum(value) as sum',
			'campaign_id',
		]);
		$subQuery->groupBy([
			LeadCost::tableName() . '.campaign_id',
		]);

		$query = new Query();
		$query->select([
			//	'c.sum',
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
			date('Y-m-d 00:00:00', strtotime($this->fromAt)),
			date('Y-m-d 23:59:59', strtotime($this->toAt)),
		]);
		$query->groupBy('c.campaign_id');

		$count = 0;
		foreach ($query->all() as $row) {
			$campaign = $row['campaign_id'];
			$cost = $row['single_cost_value'];
			$leadsIds = explode(',', $row['leads_ids']);
			$count += Lead::updateAll([
				'cost_value' => $cost,
			], [
				'campaign_id' => $campaign,
				'id' => $leadsIds,
			]);
		}

		return $query->all();
	}

	protected function getCostQuery(): LeadCostQuery {
		return LeadCost::find()
			->andWhere([
				LeadCost::tableName() . '.campaign_id' => $this->getCampaignsIds(),
			])
			->andFilterWhere([
				'>=', LeadCost::tableName() . '.date_at', $this->fromAt,
			])
			->andFilterWhere([
				'<=', LeadCost::tableName() . '.date_at', $this->toAt,
			]);
	}

	public function getLeadsDataProvider(): ActiveDataProvider {
		$query = Lead::find()
			->dateBetween($this->fromAt, $this->toAt)
			->andWhere([
				'campaign_id' => $this->getCampaignsIds(),
			]);
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => [
					'date_at' => SORT_DESC,
				],
			],
		]);
		if (!$this->validate()) {
			$query->where('0 = 1');
		}
		if (!empty($this->userId)) {
			$query->user($this->userId);
		}
		return $dataProvider;
	}

	public function setDateFromCampaigns(): void {
		$values = LeadCost::find()
			->andWhere(['campaign_id' => $this->getCampaignsIds()])
			->select(['MIN(date_at) as min, MAX(date_at) as max'])
			->asArray()
			->one();
		$this->fromAt = $values['min'] ?? null;
		$this->toAt = $values['max'] ?? null;
	}

	public function getCampaignsIds(): array {
		$ids = $this->campaignIds;
		if ($this->withChildesCampaigns) {
			foreach ($ids as $id) {
				$childes = LeadCampaign::getHierarchy()->getAllChildesIds($id);
				if (!empty($childes)) {
					$ids = array_merge($ids, $childes);
				}
			}
		}
		$ids = array_unique($ids);
		return $ids;
	}

}
