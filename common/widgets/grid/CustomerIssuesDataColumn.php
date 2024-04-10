<?php

namespace common\widgets\grid;

use common\models\issue\IssueInterface;
use Yii;

class CustomerIssuesDataColumn extends IssuesDataColumn {

	public $attribute = 'customer';
	public $customerAttribute = 'customer';

	public function init(): void {
		if ($this->label === null) {
			$this->label = Yii::t('issue', 'Customer');
		}
		parent::init();
	}

	protected function issueValue(IssueInterface $issue): string {
		if ($this->issueValue) {
			return call_user_func($this->issueValue, $issue);
		}
		//	return $issue->getIssueModel()->customer->getFullName();
		return $issue->{$this->customerAttribute};
	}
}
