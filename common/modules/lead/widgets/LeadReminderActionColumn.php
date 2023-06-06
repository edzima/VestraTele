<?php

namespace common\modules\lead\widgets;

use common\helpers\Html;
use common\helpers\Url;
use common\modules\lead\models\LeadReminder;
use common\widgets\grid\ActionColumn;
use Yii;

class LeadReminderActionColumn extends ActionColumn {

	public $controller = '/lead/reminder';

	public $template = '{not-done} {done} {view} {update} {delete}';

	public ?int $userId = null;

	public ?string $returnUrl = null;

	public function init() {
		parent::init();
		if ($this->returnUrl === null) {
			$this->returnUrl = Url::current();
		}
		$this->initDefaultVisibleButtons();
	}

	protected function initDefaultButtons() {
		parent::initDefaultButtons();
		if (!isset($this->buttons['done'])) {
			$this->buttons['done'] = function (string $url): string {
				$url .= '&returnUrl=' . $this->returnUrl;
				return Html::a(
					Html::icon('ok-circle'),
					$url,
					[
						'title' => Yii::t('lead', 'Reminder mark as done'),
						'aria-label' => Yii::t('lead', 'Reminder mark as done'),
						'data-method' => 'POST',
					]

				);
			};
		}
		if (!isset($this->buttons['not-done'])) {
			$this->buttons['not-done'] = function (string $url): string {
				$url .= '&returnUrl=' . $this->returnUrl;
				return Html::a(
					Html::icon('remove-circle'),
					$url,
					[
						'title' => Yii::t('lead', 'Reminder unmark as done'),
						'aria-label' => Yii::t('lead', 'Reminder unmark as done'),
						'data-method' => 'POST',
					]
				);
			};
		}
	}

	private function visibleNotDoneButton(LeadReminder $reminder) {
		return $reminder->reminder->isDone() && $this->isForUserOrGeneral($reminder);
	}

	private function visibleDoneButton(LeadReminder $reminder) {
		return !$reminder->reminder->isDone() && $this->isForUserOrGeneral($reminder);
	}

	private function visibleDeleteButton(LeadReminder $reminder) {
		return $this->isForUserOrGeneral($reminder);
	}

	private function visibleUdpateButton(LeadReminder $reminder) {
		return $this->isForUserOrGeneral($reminder);
	}

	private function isForUserOrGeneral(LeadReminder $reminder): bool {
		if ($this->userId === null) {
			return true;
		}
		return $reminder->reminder->user_id === null || $reminder->reminder->user_id === $this->userId;
	}

	private function initDefaultVisibleButtons() {
		if (!isset($this->visibleButtons['delete'])) {
			$this->visibleButtons['delete'] = function (LeadReminder $reminder): bool {
				return $this->visibleDeleteButton($reminder);
			};
		}

		if (!isset($this->visibleButtons['update'])) {
			$this->visibleButtons['update'] = function (LeadReminder $reminder): bool {
				return $this->visibleUdpateButton($reminder);
			};
		}

		if (!isset($this->visibleButtons['done'])) {
			$this->visibleButtons['done'] = function (LeadReminder $reminder): bool {
				return $this->visibleDoneButton($reminder);
			};
		}

		if (!isset($this->visibleButtons['not-done'])) {
			$this->visibleButtons['not-done'] = function (LeadReminder $reminder): bool {
				return $this->visibleNotDoneButton($reminder);
			};
		}
	}

}
