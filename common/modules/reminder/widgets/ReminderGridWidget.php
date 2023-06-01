<?php

namespace common\modules\reminder\widgets;

use Closure;
use common\modules\reminder\models\Reminder;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use Yii;

class ReminderGridWidget extends GridView {

	public $showOnEmpty = false;
	public $emptyText = false;
	public $summary = false;

	public array $visibleButtons = [];
	public string $actionController = '/reminder/reminder';

	public bool $visibleUserColumn = true;
	public ?Closure $urlCreator = null;

	protected const ROW_CLASS_DONE = 'success';
	protected const ROW_CLASS_DELAYED = 'danger';
	protected const ROW_CLASS_NOT_DONE_AND_NOT_DELAYED = 'warning';

	public static function htmlRowOptions(Reminder $model): array {
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
			$this->rowOptions = function (Reminder $model): array {
				return static::htmlRowOptions($model);
			};
		}

		parent::init();
	}

	protected function defaultColumns(): array {
		return [
			'date_at:datetime',
			'details',
			[
				'attribute' => 'priority',
				'value' => 'priorityName',
			],
			[
				'attribute' => 'user',
				'visible' => $this->visibleUserColumn,
			],
			'created_at:date',
			'updated_at:date',
			[
				'class' => ActionColumn::class,
				'controller' => $this->actionController,
				'urlCreator' => $this->urlCreator,
				'template' => '{update} {delete}',
				'visibleButtons' => $this->visibleButtons,
			],
		];
	}
}
