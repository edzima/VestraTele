<?php

namespace common\widgets\grid;

use common\helpers\Html;
use common\helpers\Url;
use common\modules\reminder\models\ReminderInterface;
use Yii;

class ReminderActionColumn extends ActionColumn {

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
		if (!isset($this->buttons['update'])) {
			$this->buttons['update'] = function (string $url): string {
				$url .= '&returnUrl=' . $this->returnUrl;
				return Html::a(
					Html::icon('pencil'),
					$url,
					[
						'title' => Yii::t('common', 'Update'),
						'aria-label' => Yii::t('common', 'Update'),
						'data-modal-link' => 1,
					]
				);
			};
		}
		parent::initDefaultButtons();
		if (!isset($this->buttons['done'])) {
			$this->buttons['done'] = function (string $url): string {
				$url .= '&returnUrl=' . $this->returnUrl;
				return Html::a(
					Html::icon('ok-circle'),
					$url,
					[
						'title' => Yii::t('common', 'Reminder mark as done'),
						'aria-label' => Yii::t('common', 'Reminder mark as done'),
						'data-method' => 'POST',
						'data-pjax' => 1,
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
						'title' => Yii::t('common', 'Reminder unmark as done'),
						'aria-label' => Yii::t('common', 'Reminder unmark as done'),
						'data-method' => 'POST',
					]
				);
			};
		}
	}

	private function visibleNotDoneButton(ReminderInterface $reminder) {
		return $reminder->isDone() && $this->isForUserOrGeneral($reminder);
	}

	private function visibleDoneButton(ReminderInterface $reminder) {
		return !$reminder->isDone() && $this->isForUserOrGeneral($reminder);
	}

	private function visibleDeleteButton(ReminderInterface $reminder) {
		return $this->isForUserOrGeneral($reminder);
	}

	private function visibleUdpateButton(ReminderInterface $reminder) {
		return $this->isForUserOrGeneral($reminder);
	}

	private function isForUserOrGeneral(ReminderInterface $reminder): bool {
		if ($this->userId === null) {
			return true;
		}
		return $reminder->getUserId() === null || $reminder->getUserId() === $this->userId;
	}

	private function initDefaultVisibleButtons() {
		if (!isset($this->visibleButtons['delete'])) {
			$this->visibleButtons['delete'] = function (ReminderInterface $reminder): bool {
				return $this->visibleDeleteButton($reminder);
			};
		}

		if (!isset($this->visibleButtons['update'])) {
			$this->visibleButtons['update'] = function (ReminderInterface $reminder): bool {
				return $this->visibleUdpateButton($reminder);
			};
		}

		if (!isset($this->visibleButtons['done'])) {
			$this->visibleButtons['done'] = function (ReminderInterface $reminder): bool {
				return $this->visibleDoneButton($reminder);
			};
		}

		if (!isset($this->visibleButtons['not-done'])) {
			$this->visibleButtons['not-done'] = function (ReminderInterface $reminder): bool {
				return $this->visibleNotDoneButton($reminder);
			};
		}
	}

}
