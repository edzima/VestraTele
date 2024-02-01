<?php

namespace common\modules\calendar\models;

use common\helpers\Url;
use common\modules\lead\models\Lead;

class LeadStatusDeadlineEvent extends FullCalendarEvent {

	private Lead $lead;

	public function setModel(Lead $model) {
		$this->lead = $model;
		$this->id = $this->getId();
		$this->title = $this->getTitle();
		$this->start = $this->getStart();
		$this->url = $this->getUrl();
		$this->tooltipContent = $this->getTooltipContent();
		$this->backgroundColor = $this->getBackgroundColor();
	}

	private function getId(): string {
		return $this->lead->getId();
	}

	private function getUrl(): string {
		return Url::leadView($this->lead->getId());
	}

	private function getTitle(): string {
		return $this->lead->getName() . ' - (' . $this->lead->getStatusName() . ')';
	}

	private function getStart(): string {
		return $this->lead->getDeadline();
	}

	private function getBackgroundColor(): string {
		$hours = $this->lead->getDeadlineHours();
		if ($hours > 0) {
			return 'red';
		}
		$warning = $this->lead->status->hours_deadline_warning;
		if ($warning && $hours * -1 <= $warning) {
			return 'rgb(232, 178, 15)';
		}
		return 'green';
	}

	private function getTooltipContent(): ?string {
		$reports = $this->lead->reports;
		$report = reset($reports);
		return $report ? $report->details : null;
	}
}
