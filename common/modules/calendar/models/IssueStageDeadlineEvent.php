<?php

namespace common\modules\calendar\models;

use common\helpers\Html;
use common\helpers\Url;
use common\models\issue\IssueInterface;
use common\models\issue\IssueStage;
use DateTime;
use Yii;

class IssueStageDeadlineEvent extends FullCalendarEvent {

	private static array $STAGES = [];

	public int $stageId;

	protected string $urlRoute = '/issue/issue/view';

	private IssueInterface $issue;

	public function setUrlRoute(string $route): void {
		$this->urlRoute = $route;
	}

	public function setModel(IssueInterface $issue): void {
		$this->issue = $issue;
		$this->id = $issue->getIssueId();
		$this->stageId = $issue->getIssueStageId();
		$this->title = $this->getTitle();
		$this->start = $this->getStart();
		$this->url = $this->getUrl();
		$this->tooltipContent = $this->getTooltipContent();
		$this->backgroundColor = $this->getBackgroundColor();
		$this->borderColor = $this->getBorderColor();
	}

	protected function getTitle(): string {
		return $this->issue->getIssueModel()->customer->getFullName() . ' - ' . $this->issue->getIssueModel()->lawyer->getFullName();
	}

	protected function getStart(): string {
		$model = $this->issue->getIssueModel();
		$date = new DateTime($model->stage_deadline_at);
		if ($date->format('H') === '00') {
			$updated = new DateTime($model->updated_at);
			$date->setTime($updated->format('H'), $updated->format('i'));
		}
		return $date->format(DATE_ATOM);
	}

	protected function getUrl(): string {
		return Url::to([$this->urlRoute, 'id', $this->id]);
	}

	private function getTooltipContent(): ?string {
		$tooltip = null;
		$model = $this->issue->getIssueModel();
		if ($model->newestNote) {
			$tooltip = Html::encode(
				Html::encode($model->newestNote->title) . ' [' . Yii::$app->formatter->asDate($model->newestNote->publish_at) . ']',
			);
		}
		return $tooltip;
	}

	protected function getBackgroundColor(): ?string {
		return $this->getStagesBackgroundColor();
	}

	protected function getStagesBackgroundColor(): ?string {
		return static::getStages()[$this->stageId]->calendar_background ?? null;
	}

	/**
	 * @return IssueStage[]
	 */
	public static function getStages(): array {
		if (empty(static::$STAGES)) {
			static::$STAGES = IssueStage::find()
				->andWhere('days_reminder IS NOT NULL')
				->andWhere('calendar_background IS NOT NULL')
				->orderBy('posi')
				->indexBy('id')
				->all();
		}
		return static::$STAGES;
	}

	protected function getBorderColor(): ?string {
		return $this->getStagesBackgroundColor();
	}

}