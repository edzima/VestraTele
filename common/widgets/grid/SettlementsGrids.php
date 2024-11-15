<?php

namespace common\widgets\grid;

use backend\helpers\Html;
use common\helpers\ArrayHelper;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueSettlement;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

class SettlementsGrids extends Widget {

	protected const DEFAULT_GRID_CLASS = IssuePayCalculationGrid::class;

	public ActiveDataProvider $dataProvider;

	public array $gridOptions = [];

	public string $type = self::TYPE_ALL;

	public const TYPE_ALL = 'all';

	public const TYPE_PERCENTAGE = 'percentage';

	public const TYPE_VALUES = 'values';

	public int $limit = 100;

	public bool $onlyNotEmpty = true;

	public function init(): void {
		$this->dataProvider->query->limit($this->limit);
		if ($this->dataProvider->getTotalCount() > $this->limit) {
			Yii::warning('SettlementsGrids - limit reached', __METHOD__);
		}
	}

	public function run(): string {
		switch ($this->type) {
			case self::TYPE_ALL:
				return $this->renderAll();
			case self::TYPE_PERCENTAGE:
				return $this->renderPercentage();
			case self::TYPE_VALUES:
				return $this->renderValues();
			default:
				throw new InvalidConfigException();
		}
	}

	private function renderAll(): string {
		$percentage = $this->renderPercentage();
		$values = $this->renderValues();
		$class = 'col-md-12';
		if (!empty($percentage) && !empty($values)) {
			$class = 'col-md-6';
		}

		return Html::tag('div',
			Html::tag('div', $percentage, [
				'class' => $class,
			])
			. Html::tag('div', $values, [
				'class' => $class,
			]),
			[
				'class' => 'settlements-all-grid-wrapper row',
			]
		);
	}

	private function renderPercentage(): ?string {
		$dataProvider = $this->createDataProvider(true);
		if (empty($dataProvider->getTotalCount())) {
			return null;
		}
		$config = $this->gridOptions;
		$class = ArrayHelper::remove($config, 'class', static::DEFAULT_GRID_CLASS);
		$config['type'] = IssuePayCalculationGrid::TYPE_PERCENTAGE;
		$config['dataProvider'] = $dataProvider;
		return $class::widget($config);
	}

	private function renderValues(): ?string {
		$dataProvider = $this->createDataProvider(false);
		if (empty($dataProvider->getTotalCount())) {
			return null;
		}
		$config = $this->gridOptions;
		$class = ArrayHelper::remove($config, 'class', static::DEFAULT_GRID_CLASS);

		$config['type'] = IssuePayCalculationGrid::TYPE_VALUE;
		$config['dataProvider'] = $dataProvider;
		return $class::widget($config);
	}

	protected function createDataProvider(bool $is_percentage): ArrayDataProvider {
		return new ArrayDataProvider([
			'allModels' => $this->getModels($is_percentage),
			'modelClass' => IssuePayCalculation::class,
			'key' => 'id',
		]);
	}

	protected function getModels(bool $is_percentage): array {
		return array_filter($this->dataProvider->getModels(), function (IssueSettlement $model) use ($is_percentage) {
			return $is_percentage ? $model->type->is_percentage : !$model->type->is_percentage;
		});
	}
}
