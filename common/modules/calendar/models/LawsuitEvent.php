<?php

namespace common\modules\calendar\models;

use common\helpers\Url;
use common\modules\court\models\Lawsuit;
use Yii;

class LawsuitEvent extends FullCalendarEvent {

	public const BACKGROUND_IS_APPEAL = '#df2424';

	private Lawsuit $model;

	public string $courtType;

	public int $is_appeal;

	public function setModel(Lawsuit $model): void {
		$this->model = $model;
		$this->id = $model->id;
		$this->title = $this->getTitle();
		$this->tooltipContent = $model->details;
		$this->courtType = $model->court->type;
		$this->start = $model->due_at;
		$this->url = Url::to(['/court/lawsuit/view', 'id' => $model->id]);
		$this->backgroundColor = $this->getBackgroundColor();
		$this->is_appeal = $model->is_appeal;
	}

	protected function getBackgroundColor(): ?string {
		if ($this->model->is_appeal) {
			return static::BACKGROUND_IS_APPEAL;
		}
		return null;
	}

	protected function getTitle(): string {
		$title = $this->getCourtCityName() . ' (' . $this->getCustomers() . ')';
		if ($this->model->is_appeal) {
			$title .= "\n" . Yii::t('court', 'Is Appeal');
		}
		return $title;
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
