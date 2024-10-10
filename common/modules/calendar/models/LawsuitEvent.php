<?php

namespace common\modules\calendar\models;

use common\helpers\Url;
use common\modules\court\models\Lawsuit;

class LawsuitEvent extends FullCalendarEvent {

	public const BACKGROUND_APPEAL = '#df2424';
	private Lawsuit $model;

	public string $courtType;

	public function setModel(Lawsuit $model): void {
		$this->model = $model;
		$this->id = $model->id;
		$this->title = $this->getTitle();
		$this->tooltipContent = $model->details;
		$this->courtType = $model->court->type;
		$this->start = $model->due_at;
		$this->url = Url::to(['/court/lawsuit/view', 'id' => $model->id]);
		$this->backgroundColor = $this->getBackgroundColor();
	}

	protected function getBackgroundColor(): ?string {
		if ($this->model->court->isAppeal()) {
			return static::BACKGROUND_APPEAL;
		}
		return null;
	}

	protected function getTitle(): string {
		return $this->getCourtCityName() . ' (' . $this->getCustomers() . ')';
	}

	protected function getCourtCityName(): string {
		$courtName = $this->model->court->name;
		$words = explode(" ", $courtName);
		return implode(" ", array_slice($words, 2));
	}

	protected function getCustomers(): string {
		$issues = $this->model->issues;
		$content = [];
		foreach ($issues as $issue) {
			$content[] = $issue->customer->profile->lastname;
		}
		return implode(', ', $content);
	}
}
