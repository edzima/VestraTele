<?php

namespace common\modules\lead\widgets;

use common\helpers\Html;
use common\modules\lead\controllers\IssueController;
use common\modules\lead\models\LeadIssue;
use common\widgets\grid\ActionColumn;
use Yii;

class LeadIssueActionColumn extends ActionColumn {

	/** @see IssueController */
	public $controller = '/lead/issue';
	public $template = '{confirm} {unconfirm}';
	public ?string $returnUrl = null;

	public function init(): void {
		parent::init();
		if (empty($this->visibleButtons)) {
			$this->visibleButtons = [
				'confirm' => function (LeadIssue $leadIssue): bool {
					return !$leadIssue->isConfirmed();
				},
				'unconfirm' => function (LeadIssue $leadIssue): bool {
					return $leadIssue->isConfirmed();
				},
			];
		}
		$this->buttons['confirm'] = function ($url, $model, $key): string {
			return Html::a(
				Html::icon('check'),
				$url, [
					'title' => \Yii::t('lead', 'Confirm'),
					'aria-label' => Yii::t('lead', 'Confirm'),
					'data-method' => 'POST',
				]
			);
		};

		$this->buttons['unconfirm'] = function ($url, $model, $key): string {
			return Html::a(
				Html::icon('unchecked'),
				$url, [
					'title' => Yii::t('lead', 'Unconfirm'),
					'aria-label' => Yii::t('lead', 'Unconfirm'),
					'data-method' => 'POST',
				]
			);
		};
	}

	public function createUrl($action, $model, $key, $index) {
		$url = parent::createUrl($action, $model, $key, $index);
		$actions = ['confirm', 'unconfirm'];
		if ($this->returnUrl && in_array($action, $actions, true)) {
			$url .= '&returnUrl=' . $this->returnUrl;
		}
		return $url;
	}
}
