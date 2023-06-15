<?php

namespace common\modules\reminder\widgets;

use common\modules\reminder\models\ReminderInterface;
use common\widgets\grid\ReminderActionColumn;
use common\widgets\GridView;
use Yii;

class ReminderGridWidget extends GridView {

	public $showOnEmpty = false;
	public $emptyText = false;
	public $summary = false;

	public bool $visibleUserColumn = true;

	public array $actionColumn = [];

	protected const ROW_CLASS_DONE = 'success';
	protected const ROW_CLASS_DELAYED = 'danger';
	protected const ROW_CLASS_NOT_DONE_AND_NOT_DELAYED = 'warning';

	public static function htmlRowOptions(ReminderInterface $model): array {
		if ($model->isDone()) {
			return [
				'class' => static::ROW_CLASS_DONE,
			];
		}
		if ($model->isDelayed()) {
			return [
				'class' => static::ROW_CLASS_DELAYED,
			];
		}
		return [
			'class' => static::ROW_CLASS_NOT_DONE_AND_NOT_DELAYED,
		];
	}

	public function init(): void {
		if (empty($this->columns)) {
			$this->columns = $this->defaultColumns();
		}
		if ($this->caption === null) {
			$this->caption = Yii::t('common', 'Reminders');
		}

		if (empty($this->rowOptions)) {
			$this->rowOptions = function (ReminderInterface $model): array {
				return static::htmlRowOptions($model);
			};
		}

		parent::init();
	}

	protected function defaultColumns(): array {
		return [
			'reminder.date_at:datetime',
			'reminder.details',
			[
				'attribute' => 'reminder.priority',
				'value' => 'reminder.priorityName',
			],
			[
				'attribute' => 'reminder.user',
				'visible' => $this->visibleUserColumn,
			],
			'reminder.created_at:date',
			'reminder.updated_at:date',
			$this->getActionColumnConfig(),
		];
	}

	protected function getActionColumnConfig(): array {
		$options = $this->actionColumn;
		if (!isset($options['class'])) {
			$options['class'] = ReminderActionColumn::class;
		}
		return $options;
	}
}
