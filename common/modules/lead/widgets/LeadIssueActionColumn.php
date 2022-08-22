<?php

namespace common\modules\lead\widgets;

use common\modules\lead\controllers\IssueController;
use common\modules\lead\models\LeadIssue;
use common\widgets\grid\ActionColumn;

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
	}

	/**
	 * {@inheritdoc}
	 */
	protected function initDefaultButtons() {
		$this->initDefaultButton('confirm', 'check', [
			'data-method' => 'post',
		]);
		$this->initDefaultButton('unconfirm', 'unchecked', [
			'data-method' => 'post',
		]);
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
