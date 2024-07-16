<?php

namespace common\modules\lead\widgets;

use common\helpers\Html;
use common\modules\lead\models\LeadStatus;
use common\widgets\GridView;
use Yii;
use yii\base\Widget;
use yii\data\ArrayDataProvider;

class LeadStatusDetailsWidget extends Widget {

	/**
	 * @var LeadStatus[]
	 */
	public array $statuses = [];

	public function init(): void {
		parent::init();
		if (empty($this->statuses)) {
			$this->statuses = $this->getDefaultStatuses();
		}
	}

	public function run() {
		if (empty($this->statuses)) {
			return '';
		}
		$models = $this->statuses;
		return GridView::widget([
			'dataProvider' => new ArrayDataProvider([
				'allModels' => $models,
				'modelClass' => LeadStatus::class,
			]),
			'columns' => [
				'name',
				'description',
			],
			'summary' => false,
			'caption' => Yii::t('lead', 'Statuses'),
			'rowOptions' => function (LeadStatus $model) {
				$options = [];
				$options['data-id'] = $model->id;
				if (!empty($model->chart_color)) {
					Html::addCssStyle($options, [
						'background-color' => $model->chart_color,
					]);
				}
				return $options;
			},
		]);
	}

	public function getDefaultStatuses(): array {
		$models = LeadStatus::getModels();
		return array_filter($models, function (LeadStatus $model) {
			return !empty($model->description);
		});
	}
}
