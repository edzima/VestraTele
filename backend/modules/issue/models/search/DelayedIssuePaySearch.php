<?php

namespace backend\modules\issue\models\search;

class DelayedIssuePaySearch extends IssuePaySearch {

	public function rules(): array {
		return array_merge(parent::rules(), [
			['status', 'compare', 'compareValue' => static::PAY_STATUS_DELAYED],
		]);
	}

	public function init() {
		parent::init();
		$this->setPayStatus(static::PAY_STATUS_DELAYED);
	}

}
