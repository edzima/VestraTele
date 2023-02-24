<?php

namespace common\modules\issue\widgets;

use common\helpers\Html;
use common\helpers\Url;
use common\models\issue\SummonDocLink;
use common\models\user\Worker;
use common\widgets\grid\ActionColumn;
use Yii;

class SummonDocsLinkActionColumn extends ActionColumn {

	public ?string $status = null;

	public string $returnUrl = '';

	public $template = '{done} {not-done} {confirm}';

	public function init(): void {
		if (empty($this->returnUrl)) {
			$this->returnUrl = Url::current();
		}

		if (!isset($this->buttons['done'])) {
			$this->buttons['done'] = function (string $url, SummonDocLink $docLink): string {
				return $this->doneButton($url, $docLink);
			};
		}

		if (!isset($this->buttons['not-done'])) {
			$this->buttons['not-done'] = function (string $url, SummonDocLink $docLink): string {
				return $this->notDoneButton($url, $docLink);
			};
		}

		if (!isset($this->buttons['confirm'])) {
			$this->buttons['confirm'] = function (string $url, SummonDocLink $docLink): string {
				return $this->confirmButton($url, $docLink);
			};
		}

		if (!isset($this->buttons['not-confirmed'])) {
			$this->buttons['not-confirmed'] = function (string $url, SummonDocLink $docLink): string {
				return $this->notConfirmButton($url, $docLink);
			};
		}
		if (!isset($this->visibleButtons['done'])) {
			$this->visibleButtons['done'] = function (SummonDocLink $docLink): bool {
				return $this->visibleDoneButton($docLink);
			};
		}

		if (!isset($this->visibleButtons['not-done'])) {
			$this->visibleButtons['not-done'] = function (SummonDocLink $docLink): bool {
				return $this->visibleNotDoneButton($docLink);
			};
		}

		if (!isset($this->visibleButtons['confirm'])) {
			$this->visibleButtons['confirm'] = function (SummonDocLink $docLink): bool {
				return $this->visibleConfirmButton($docLink);
			};
		}

		if (!isset($this->visibleButtons['not-confirmed'])) {
			$this->visibleButtons['not-confirmed'] = function (SummonDocLink $docLink): bool {
				return $this->visibleNotConfirmedButton($docLink);
			};
		}

		switch ($this->status) {
			case SummonDocLink::STATUS_TO_DO:
				$this->template = '{done} {confirm}';
				$this->buttons = [
					'done' => function (string $url, SummonDocLink $docLink): string {
						if ($docLink->summon->isContractor(Yii::$app->user->getId()) || Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER)) {
							$url .= '&returnUrl=' . $this->returnUrl;
							return
								Html::a(
									Html::icon('check'),
									$url, [
									'title' => Yii::t('issue', 'To Confirm'),
									'aria-label' => Yii::t('issue', 'To Confirm'),
									'data-method' => 'POST',
								]);
						}
						return '';
					},
					'confirm' => function (string $url, SummonDocLink $docLink): string {
						if ($docLink->summon->isOwner(Yii::$app->user->getId()) || Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER)) {
							$url .= '&returnUrl=' . $this->returnUrl;
							return
								Html::a(
									Html::icon('ok'),
									$url, [
									'title' => Yii::t('issue', 'Confirmed'),
									'aria-label' => Yii::t('issue', 'Confirmed'),
									'data-method' => 'POST',
								]);
						}
						return '';
					},

				];
				break;
			case SummonDocLink::STATUS_TO_CONFIRM:
				$this->template = '{confirm} {not-done}';
				$this->buttons = [
					'confirm' => function (string $url, SummonDocLink $docLink): string {
						if ($docLink->summon->isOwner(Yii::$app->user->getId())
							|| Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER)
						) {
							$url .= '&returnUrl=' . $this->returnUrl;
							return
								Html::a(
									Html::icon('ok'),
									$url, [
									'title' => Yii::t('issue', 'Confirm'),
									'aria-label' => Yii::t('issue', 'Confirm'),
									'data-method' => 'POST',
								]);
						}
						return '';
					},
					'not-done' => function (string $url, SummonDocLink $docLink): string {
						if ($docLink->done_user_id === Yii::$app->user->getId()
							|| Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER)
						) {
							$url .= '&returnUrl=' . $this->returnUrl;
							return
								Html::a(
									Html::icon('remove'),
									$url, [
									'title' => Yii::t('issue', 'To Do'),
									'aria-label' => Yii::t('issue', 'To Do'),
									'data-method' => 'POST',
								]);
						}
						return '';
					},
				];
				break;

			case SummonDocLink::STATUS_CONFIRMED:
				$this->template = '{not-confirmed}';
				break;
		}
		parent::init();
	}

	private function visibleDoneButton(SummonDocLink $docLink): bool {
		return
			!(// owner or manager only confirm action without double stage
				Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER)
				|| $docLink->summon->isOwner(Yii::$app->user->getId())
			)
			&& empty($docLink->done_at)
			&& $docLink->summon->isContractor(Yii::$app->user->getId());
	}

	private function doneButton(string $url, SummonDocLink $docLink): string {
		return
			Html::a(
				Html::icon('check'),
				$url, [
				'title' => Yii::t('issue', 'To Confirm'),
				'aria-label' => Yii::t('issue', 'To Confirm'),
				'data-method' => 'POST',
			]);
	}

	private function visibleNotDoneButton(SummonDocLink $docLink): bool {
		return ($docLink->isToConfirm() || $docLink->isConfirmed())
			&& (
				$docLink->done_user_id === Yii::$app->user->getId()
				|| $docLink->summon->isOwner(Yii::$app->user->getId())
				|| Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER)
			);
	}

	private function notDoneButton(string $url, SummonDocLink $docLink): string {
		return
			Html::a(
				Html::icon('remove'),
				$url, [
				'title' => Yii::t('issue', 'To Do'),
				'aria-label' => Yii::t('issue', 'To Do'),
				'data-method' => 'POST',
			]);
	}

	private function visibleConfirmButton(SummonDocLink $docLink): bool {
		return !$docLink->isConfirmed()
			&& (
				$docLink->summon->isOwner(Yii::$app->user->getId())
				|| Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER)
			);
	}

	private function confirmButton(string $url, SummonDocLink $docLink): string {
		return
			Html::a(
				Html::icon('ok'),
				$url, [
				'title' => Yii::t('issue', 'Confirmed'),
				'aria-label' => Yii::t('issue', 'Confirmed'),
				'data-method' => 'POST',
			]);
	}

	private function visibleNotConfirmedButton(SummonDocLink $docLink): bool {
		return $docLink->confirmed_at === Yii::$app->user->getId()
			|| Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER);
	}

	private function notConfirmButton(string $url, SummonDocLink $docLink): string {
		return
			Html::a(
				Html::icon('remove'),
				$url, [
				'title' => Yii::t('issue', 'To Confirm'),
				'aria-label' => Yii::t('issue', 'To Confirm'),
				'data-method' => 'POST',
			]);
	}

	function createUrl($action, $model, $key, $index) {
		assert($model instanceof SummonDocLink);
		$key = [
			'summon_id' => $model->summon_id,
			'doc_type_id' => $model->doc_type_id,
		];
		return parent::createUrl($action, $model, $key, $index) . '&returnUrl=' . $this->returnUrl;
	}
}
