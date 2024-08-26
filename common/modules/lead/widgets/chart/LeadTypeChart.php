<?php

namespace common\modules\lead\widgets\chart;

use common\modules\lead\models\LeadType;
use common\widgets\charts\ChartsWidget;
use Yii;
use yii\base\InvalidConfigException;

class LeadTypeChart extends ChartsWidget {

	public ?array $typesCount = null;

	public string $type = self::TYPE_DONUT;

	public bool $showDonutTotalLabels = true;
	public bool $legendFormatterAsSeriesWithCount = true;

	public function init(): void {
		if (empty($this->series)) {
			if ($this->typesCount === null) {
				throw new InvalidConfigException('typesCount must be set when series is empty.');
			}
			$this->series = array_values($this->typesCount);
			$this->options['labels'] = $this->getLabelsFromTypesIds(array_keys($this->typesCount));
		}
		if (!isset($this->options['title'])) {
			$this->options['title'] = $this->defaultTitleOptions();
		}
		parent::init();
	}

	private function getLabelsFromTypesIds(array $ids): array {
		$labels = [];
		foreach ($ids as $id) {
			$labels[] = LeadType::getNames()[$id];
		}
		return $labels;
	}

	private function defaultTitleOptions(): array {
		return [
			'text' => Yii::t('lead', 'Types Count'),
			'align' => 'center',
		];
	}
}
