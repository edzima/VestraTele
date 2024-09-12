<?php

namespace common\modules\lead\widgets;

use common\modules\lead\components\cost\StatusCost;
use common\modules\lead\models\LeadDealStage;
use yii\widgets\DetailView;

/**
 * @property  StatusCost $model
 */
class StatusDealStageDetailView extends DetailView {

	public const STAGES_CLOSED_WON_WITH_CONTRACT_SENT = [
		LeadDealStage::DEAL_STAGE_CLOSED_WON,
		[
			LeadDealStage::DEAL_STAGE_CLOSED_WON,
			LeadDealStage::DEAL_STAGE_CONTRACT_SENT,
		],
	];

	public array $dealStages = self::STAGES_CLOSED_WON_WITH_CONTRACT_SENT;

	public function init(): void {
		if (empty($this->attributes)) {
			$this->attributes = static::attributesFromStatusCost($this->model, $this->dealStages);
		}
		parent::init();
	}

	public static function attributesFromStatusCost(StatusCost $model, array $dealStages = []): array {
		$attributes = [];
		foreach ($dealStages as $stages) {
			if (is_int($stages)) {
				$value = $model->getDealStagesCosts()[$stages] ?? null;
				$attributes[] = [
					'label' => $model->getDealStageName($stages),
					'value' => (string) $value,
					'format' => 'currency',
					'visible' => isset($value),
				];
			} elseif (is_array($stages)) {
				$labels = [];
				$counts = [];
				foreach ($stages as $dealStage) {
					$labels[] = $model->getDealStageName($dealStage);
					$counts[] = $model->getDealStagesCounts()[$dealStage] ?? 0;
				}
				$sum = array_sum($counts);

				$value = $sum
					? $model->getCostSum() / array_sum($counts)
					: null;
				$attributes[] = [
					'label' => implode(', ', $labels),
					'value' => (string) $value,
					'format' => 'currency',
					'visible' => isset($value),
				];
			}
		}
		return $attributes;
	}

}
