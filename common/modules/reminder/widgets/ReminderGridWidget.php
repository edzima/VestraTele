<?php

namespace common\modules\reminder\widgets;

use Closure;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use Yii;

class ReminderGridWidget extends GridView {

	public $showOnEmpty = false;
	public $emptyText = false;
	public $summary = false;
	public string $actionController = '/reminder/reminder';
	public ?Closure $urlCreator = null;

	public function init(): void {
		if (empty($this->columns)) {
			$this->columns = $this->defaultColumns();
		}
		if ($this->caption === null) {
			$this->caption = Yii::t('common', 'Reminders');
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
			'created_at:date',
			'updated_at:date',
			[
				'class' => ActionColumn::class,
				'controller' => $this->actionController,
				'urlCreator' => $this->urlCreator,
				'template' => '{update} {delete}',
			],
		];
	}
}
