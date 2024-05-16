<?php

namespace common\modules\calendar\models;

use common\helpers\Html;
use common\helpers\Url;
use common\models\issue\Summon;
use common\modules\reminder\models\Reminder;
use Yii;

class SummonCalendarEvent extends FullCalendarEvent {

	public const IS_SUMMON = 'summon';
	public const IS_DEADLINE = 'deadline';

	public const IS_REMINDER = 'reminder';
	public string $is = self::IS_SUMMON;

	public array $borderColors = [
		self::IS_DEADLINE => '#c44119',
		self::IS_REMINDER => '#ffbf00',
	];

	public int $statusId;
	public ?string $phone = null;
	public int $typeId;
	public ?string $eventBorderColor = null;

	protected string $urlRoute = '/summon/view';

	private ?Summon $model = null;
	private ?Reminder $reminder = null;

	public function setUrlRoute(string $route): void {
		$this->urlRoute = $route;
	}

	public static function getStatusesBackgroundColors(): array {
		return [
			Summon::STATUS_NEW => '#00cc99',
			Summon::STATUS_IN_PROGRESS => '#C2185B',
			Summon::STATUS_WITHOUT_RECOGNITION => '#7B1FA2',
			Summon::STATUS_TO_CONFIRM => '#FF9100',
			Summon::STATUS_REALIZED => '#303F9F',
			Summon::STATUS_UNREALIZED_CLIENT => '#616161',
			Summon::STATUS_UNREALIZED_COMPANY => '#CFCFCF',
		];
	}

	public static function find(int $id): ?self {
		$model = Summon::findOne($id);
		if ($model === null) {
			return null;
		}
		$self = new static();
		$self->setModel($model);
		return $self;
	}

	public function rules(): array {
		return [
			['start', 'datetime', 'format' => 'php:' . DATE_ATOM],
		];
	}

	public function updateDate(string $start): bool {
		$this->start = $start;
		if (!$this->validate('start')) {
			return false;
		}
		if ($this->getModel() === null) {
			$this->addError('id', 'Not Found Model for This ID');
			return false;
		}
		$model = $this->getModel();
		$model->realize_at = $start;
		return $model->updateAttributes([
			'realize_at',
		]);
	}

	public function getModel(): ?Summon {
		if ($this->model === null) {
			$this->model = Summon::findOne($this->id);
		}
		return $this->model;
	}

	public function setModel(Summon $model): void {
		$this->model = $model;
		$this->id = $model->id;
		$this->statusId = $model->status;
		$this->typeId = $model->type_id;
		$this->start = $this->getStart();
		$this->title = $this->getTitle();
		$this->phone = $this->getPhone();
		$this->url = $this->getUrl();
		$this->backgroundColor = $this->getBackgroundColor();
		$this->borderColor = $this->getBorderColor();
		$this->tooltipContent = $this->getTooltipContent();
	}

	protected function getTitle(): string {
		$customer = $this->model->issue->customer;
		return Html::encode($customer->getFullName());
	}

	protected function getPhone() {
		$customer = $this->model->issue->customer;

		return Yii::$app->formatter->asTel($customer->getPhone(), [
			'nullDisplay' => null,
			'asLink' => false,
		]);
	}

	public function setReminder(Reminder $reminder) {
		$this->reminder = $reminder;
		$this->id = $this->model->id . ', ' . $this->reminder->id;
	}

	protected function getUrl(): string {
		return Url::to([$this->urlRoute, 'id' => $this->getModel()->id]);
	}

	protected function getStart(): string {
		switch ($this->is) {
			case static::IS_DEADLINE:
				return $this->getModel()->deadline_at;
			case static::IS_REMINDER:
				return $this->reminder->date_at;
			default:
				return $this->getModel()->realize_at;
		}
	}

	protected function getBorderColor(): string {
		return $this->borderColors[$this->is] ?? $this->getBackgroundColor();
	}

	protected function getBackgroundColor(): string {
		return static::getStatusesBackgroundColors()[$this->getModel()->status];
	}

	protected function getTooltipContent(): ?string {
		if ($this->reminder !== null && $this->is === static::IS_REMINDER) {
			$title = $this->reminder->details;
		} else {
			$title = $this->getModel()->getTitleWithDocs();
		}
		if ($title) {
			return Html::encode($title);
		}
		return null;
	}

}
