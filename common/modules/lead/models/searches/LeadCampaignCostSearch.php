<?php

namespace common\modules\lead\models\searches;

use common\modules\lead\components\cost\StatusCost;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadCampaign;
use common\modules\lead\models\LeadCost;
use common\modules\lead\models\query\LeadCostQuery;
use common\modules\lead\widgets\chart\LeadStatusChart;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class LeadCampaignCostSearch extends Model {

	public array $campaignIds = [];

	public ?string $fromAt;
	public $toAt;

	public $userId;

	public bool $withChildesCampaigns = true;

	public const SCENARIO_USER = 'user';

	private ?float $costSum = null;
	private ?int $leadsTotalCount = null;

	private ?StatusCost $statusCost = null;

	private array $instances = [];

	public function getOrCreateForCampaignIds(array $campaignIds): LeadCampaignCostSearch {
		$key = implode(',', $campaignIds);
		if (!isset($this->instances[$key])) {
			$model = new self();
			$model->fromAt = $this->fromAt;
			$model->toAt = $this->toAt;
			$model->userId = $this->userId;
			$model->campaignIds = $campaignIds;
			$this->instances[$key] = $model;
		}
		return $this->instances[$key];
	}

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

	public function getCostSum(bool $refresh = false): ?float {
		if ($this->costSum === null || $refresh) {
			$this->costSum = $this->getCostQuery()->sum('value');
		}
		return $this->costSum;
	}

	public function getLeadsTotalCount(bool $refresh = false): int {
		if ($this->leadsTotalCount === null || $refresh) {
			$this->leadsTotalCount = $this->getLeadsDataProvider()->getTotalCount();
		}
		return $this->leadsTotalCount;
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

	public function getStatusCost(bool $refresh = false): StatusCost {
		if ($this->statusCost === null || $refresh) {
			$statusCost = new StatusCost();
			$statusCost->setStatusCounts($this->getStatusCount());
			$statusCost->setCostSum((float) $this->getCostSum());
			$this->statusCost = $statusCost;
		}
		return $this->statusCost;
	}

	public function getStatusCount(): array {
		$widget = new  LeadStatusChart([
			'query' => $this->getLeadsDataProvider()->query,
		]);
		return $widget->statuses;
	}

}
