<?php

namespace common\modules\calendar\models;

use common\helpers\Url;
use common\modules\lead\models\LeadReminder;
use common\modules\lead\models\LeadStatus;
use common\modules\reminder\models\Reminder;
use DateTime;

class LeadReminderCalendarEvent extends FullCalendarEvent {

	private LeadReminder $model;

	public int $priority;

	public static function getPriorityColors(): array {
		return [
			Reminder::PRIORITY_LOW => '#d4edda',
			Reminder::PRIORITY_MEDIUM => '#ffeeba',
			Reminder::PRIORITY_HIGH => 'rgb(204, 0, 0)',
		];
	}

	public function setModel(LeadReminder $model): void {
		$this->model = $model;
		$this->id = $this->getId();
		$this->title = $this->getTitle();
		$this->start = $this->getStart();
		$this->url = Url::leadView($model->lead_id);
		$this->tooltipContent = $this->getTooltipContent();
		$this->priority = $model->reminder->priority;
		$this->backgroundColor = $this->getBackgroundColor();
		$this->borderColor = $this->getBorderColor();
	}

	protected function getId(): string {
		return $this->model->reminder_id;
	}

	protected function getBackgroundColor(): ?string {
		return LeadStatus::getModels()[$this->model->lead->status_id]->calendar_background ?? null;
	}

	protected function getStart(): string {
		return (new DateTime($this->model->reminder->date_at))
			->format(DATE_ATOM);
	}

	protected function getTitle(): string {
		return $this->model->lead->getName();
	}

	private function getTooltipContent(): ?string {
		return $this->model->reminder->details;
	}

	private function getBorderColor() {
		return static::getPriorityColors()[$this->priority];
	}
}
