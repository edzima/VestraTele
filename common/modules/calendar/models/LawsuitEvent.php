<?php

namespace common\modules\calendar\models;

use common\helpers\Url;
use common\modules\court\models\Lawsuit;
use common\modules\court\models\LawsuitSession;
use Yii;

class LawsuitEvent extends FullCalendarEvent {

	public const BACKGROUND_IS_APPEAL = '#df2424';

	private Lawsuit $model;
	private LawsuitSession $session;

	public string $courtType;

	public int $is_appeal;

	public int $has_url;
	public int $is_canceled;

	public function setSession(LawsuitSession $session) {
		$this->session = $session;
		$this->setModel($session->lawsuit);
		$this->start = $session->date_at;
		$this->is_canceled = $session->is_cancelled;
		$this->has_url = !empty($session->url);
		if ($session->is_cancelled) {
			$this->classNames[] = static::CLASS_TRANSPARENT;
		}
	}

	public function setModel(Lawsuit $model): void {
		$this->model = $model;
		$this->id = $model->id;
		$this->title = $this->getTitle();
		$this->tooltipContent = $model->details;
		$this->courtType = $model->court->type;
		$this->url = Url::to(['/court/lawsuit/view', 'id' => $model->id]);
		$this->backgroundColor = $this->getBackgroundColor();
		$this->is_appeal = $model->is_appeal;
		$this->borderColor = $this->getBorderColor();
	}

	protected function getBackgroundColor(): ?string {
		if ($this->model->is_appeal) {
			return static::BACKGROUND_IS_APPEAL;
		}
		return null;
	}

	protected function getBorderColor(): ?string {
		if (!empty($this->session->url)) {
			return 'lime';
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
