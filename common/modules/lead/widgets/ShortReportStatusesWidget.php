<?php

namespace common\modules\lead\widgets;

use common\helpers\Html;
use common\modules\lead\models\LeadStatus;
use yii\base\Widget;

class ShortReportStatusesWidget extends Widget {

	public int $lead_id;
	public string $route = '/lead/report/short-status';
	public array $options = [
		'class' => 'btn btn-info',
	];

	private static array $models;

	public function init() {
		parent::init();
		$this->options['data-method'] = 'POST';
	}

	public function run(): string {
		$models = static::getModels();
		if (empty($models)) {
			return '';
		}
		$content = [];
		foreach ($models as $model) {
			$content[] = Html::a(
				$model->getName(),
				[$this->route, 'lead_id' => $this->lead_id, 'status_id' => $model->getId()],
				$this->options);
		}
		return implode(' ', $content);
	}

	/**
	 * @return LeadStatus[]
	 */
	public static function getModels(): array {
		if (empty(static::$models)) {
			static::$models = LeadStatus::find()->andWhere(['short_report' => true])->all();
		}
		return static::$models;
	}
}
