<?php

namespace common\modules\lead\widgets\chart;

use common\helpers\ArrayHelper;
use common\modules\lead\models\LeadCampaign;
use common\modules\lead\models\LeadCost;
use common\modules\lead\models\query\LeadCostQuery;
use common\widgets\charts\ChartsWidget;
use Yii;
use yii\base\Widget;

class CampaignCostChart extends Widget {

	public array $data = [];

	public array $series = [];

	public string $withoutCampaignName = '';

	/**
	 * @var LeadCampaign[]
	 */
	public array $campaigns = [];

	public array $chartOptions = [];

	public bool $orderByCount = true;

	public static function costDayData(LeadCostQuery $query) {
		$query = clone $query;
		$query->select([
			LeadCost::tableName() . '.date_at',
			LeadCost::tableName() . '.campaign_id',
			LeadCost::tableName() . '.value',
			'count(lead.id) as leadsCount',
		]);
		$query->joinWith('leads', false);
		$query->groupBy([
			LeadCost::tableName() . '.campaign_id',
			LeadCost::tableName() . '.date_at',
		]);
		$query->asArray();
		$data = $query->all();
		$data = ArrayHelper::index($data, 'date_at', 'campaign_id');
		$series = [];
		$yAxis = [];
		$campaignsCount = count($data);
		$campaignNames = LeadCampaign::find()->andWhere([
			'id' => array_keys($data),
		])
			->select('name')
			->indexBy('id')
			->column();

		foreach ($data as $campaignId => $rows) {
			if (!isset($series[$campaignId])) {
				$name = $campaignsCount > 1 ? $campaignNames[$campaignId] : null;

				$series[$campaignId . '-count'] = [
					'name' => $name
						? Yii::t('lead', '{name} - Leads', [
							'name' => $name,
						])
						: Yii::t('lead', 'Leads Count'),
					'data' => [],
					'campaign_id' => $campaignId,
					'group' => 'count',
					'type' => ChartsWidget::TYPE_BAR,
					'strokeWidth' => 0,
				];
				$series[$campaignId . '-cost'] = [
					'name' => $name
						? Yii::t('lead', '{name} - Cost', [
							'name' => $name,
						])
						: Yii::t('lead', 'Cost'),
					'data' => [],
					'campaign_id' => $campaignId,
					'group' => 'cost',
					'type' => ChartsWidget::TYPE_LINE,
					'strokeWidth' => 3,
				];
				$series[$campaignId . '-avg'] = [
					'name' => $name
						? Yii::t('lead', '{campaignName} - Single Lead Cost', [
							'campaignName' => $name,
						])
						: Yii::t('lead', 'Single Lead cost Value'),
					'data' => [],
					'campaign_id' => $campaignId,
					'group' => 'avg',
					'type' => ChartsWidget::TYPE_AREA,
					'strokeWidth' => 2,
				];
				$yAxis['seriesNames.leads'][] = $series[$campaignId . '-count']['name'];
				$yAxis['seriesNames.cost'][] = $series[$campaignId . '-cost']['name'];
				$yAxis['seriesNames.avg'][] = $series[$campaignId . '-avg']['name'];
			}
			foreach ($rows as $row) {
				$cost = $row['value'] ? (float) $row['value'] : null;
				$count = $row['leadsCount'] ? (int) $row['leadsCount'] : null;
				$avg = $cost && $count ? $cost / $count : null;
				$series[$campaignId . '-cost']['data'][] = [
					'x' => $row['date_at'],
					'y' => $cost ? round($cost, 1) : null,
				];
				$series[$campaignId . '-count']['data'][] = [
					'x' => $row['date_at'],
					'y' => $count,
				];
				$series[$campaignId . '-avg']['data'][] = [
					'x' => $row['date_at'],
					'y' => $avg ? round($avg, 1) : null,
				];
			}
		}
		return [
			'series' => array_values($series),
			'yAxis' => $yAxis,
		];
	}

	protected function getDefaultChartOptions(): array {
		return [
			'type' => ChartsWidget::TYPE_LINE,
			'height' => 420,
			'options' => [
				'title' => [
					'text' => Yii::t('lead', 'Campaigns'),
				],
			],
		];
	}

	public function init(): void {
		parent::init();
		if (empty($this->withoutCampaignName)) {
			$this->withoutCampaignName = Yii::t('lead', 'Without Campaign');
		}

		$this->orderData();

		$parents = $this->parentsMap();
		//echo Html::dump($parents);
	}

	public function run(): string {
		$options = $this->chartOptions();
		return ChartsWidget::widget($options);
	}

	protected function chartOptions(): array {
		$options = ArrayHelper::merge($this->chartOptions, $this->getDefaultChartOptions());
		if (!isset($options['series'])) {
			$series = $this->series();
			$options['series'] = $series;
			$options['options']['labels'] = array_values($this->labels());
			$options['options']['stroke']['width'] = ArrayHelper::getColumn($series, 'strokeWidth');
		}
		return $options;
	}

	protected function series(): array {
		if (!empty($this->series)) {
			return $this->series;
		}
		$series = [];
		$series[] = $this->countSeries();
		return $series;
	}

	protected function countSeries(): array {
		return [
			'name' => Yii::t('lead', 'Counts'),
			'data' => array_values($this->countSeriesData()),
			'type' => ChartsWidget::TYPE_COLUMN,
			'strokeWidth' => 0,
		];
	}

	public function countSeriesData(): array {
		$counts = ArrayHelper::map($this->data, 'campaign_id', 'count');
		return array_map('intval', $counts);
	}

	public function labels(): array {
		$ids = $this->getCampaignsIds();
		if (empty($ids)) {
			return [];
		}
		$labels = [];
		foreach ($ids as $id) {
			$labels[$id] = $this->label($id);
		}
		return $labels;
	}

	protected function label(?int $id): string {
		if (empty($id)) {
			return $this->withoutCampaignName;
		}
		return $this->getCampaigns()[$id]->name;
	}

	/**
	 * indexed by id
	 *
	 * @return LeadCampaign[]
	 */
	protected function getCampaigns(): array {
		$ids = $this->getCampaignsIds();
		if (empty($ids)) {
			return [];
		}
		if (empty($this->campaigns)) {
			$this->campaigns = LeadCampaign::find()
				->andWhere(['id' => $ids])
				->with('parent')
				->indexBy('id')
				->all();
		}
		return $this->campaigns;
	}

	public function getCampaignsIds() {
		return ArrayHelper::getColumn($this->data, 'campaign_id');
	}

	private function countsGroupParents() {
		$data = [];
		$parents = $this->parentsMap();
		$counts = $this->countSeriesData();
	}

	private function parentsMap(): array {
		$campaigns = $this->getCampaigns();
		$data = [];
		$tree = LeadCampaign::getHierarchy()->getTree();
		//echo Html::dump($tree);
		$parentsTree = [];
		$counts = $this->countSeriesData();
		foreach ($campaigns as $id => $campaign) {
			if ($campaign->getParentId()) {
				$parent = $tree[$campaign->getParentId()];
				$parent['count'][$campaign->id] = $counts[$campaign->id];
				$parentsTree[$campaign->parent_id] = $parent;
			}
			$data[$id] = $campaign->getParentsIds();
		}
		//	echo Html::dump($parentsTree);
		return $data;
	}

	protected function orderData(): void {
		if ($this->orderByCount) {
			usort($this->data, function (array $a, array $b) {
				return $b['count'] <=> $a['count'];
			});
		}
	}

}
