<?php

namespace common\modules\calendar\controllers;

use common\modules\calendar\models\searches\LeadCalendarSearch;
use common\modules\lead\models\LeadReminder;
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;

class LeadController extends Controller {

	public function actionIndex(): string {
		$model = new LeadCalendarSearch();
		$filters = [];
		foreach ($model->getFilters() as $filter) {
			$filters[] = $filter->toArray();
		}
		return $this->render('index', [
			'userId' => Yii::$app->user->getId(),
			'filters' => $filters,
		]);
	}

	public function actionList(string $start = null, string $end = null): Response {
		if ($start === null) {
			$start = date('Y-m-01');
		}
		if ($end === null) {
			$end = date('Y-m-t 23:59:59');
		}
		$model = new LeadCalendarSearch();
		$model->user_id = Yii::$app->user->getId();
		$model->dateStart = $start;
		$model->dateEnd = $end;
		$data = [];

		foreach ($model->search()->getModels() as $reminder) {
			/**
			 * @var LeadReminder $reminder
			 */
			$data[] = [
				'id' => $reminder->lead_id,
				'url' => Url::to(['/lead/lead/view', 'id' => $reminder->lead_id]),
				'title' => $reminder->lead->name,
				'start' => $reminder->reminder->date_at,
				'phone' => Html::encode($reminder->lead->getPhone()),
				'statusId' => $reminder->lead->status_id,
				'tooltipContent' => $this->getTooltipContent($reminder),
			];
		}

		return $this->asJson($data);
	}

	private function getTooltipContent(LeadReminder $model): string {
		$tooltip = $model->reminder->details;
		$phone = $model->lead->getPhone();
		if (!empty($phone)) {
			$tooltip .= ' Tel: ' . $phone;
		}
		return $tooltip;
	}
}
