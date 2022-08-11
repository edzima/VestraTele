<?php

namespace common\modules\lead\widgets;

use common\modules\lead\models\LeadIssue;
use common\widgets\grid\ActionColumn;

class LeadIssueActionColumn extends ActionColumn {

	public $controller = '/lead/issue';
	public $template = '{confirm} {unconfirm}';

	public function init() {
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
}
