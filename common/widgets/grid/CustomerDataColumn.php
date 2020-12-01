<?php

namespace common\widgets\grid;

use Yii;

class CustomerDataColumn extends DataColumn {

	public $attribute = 'customerLastname';
	public $value = 'issue.customer.fullName';

	public function init(): void {
		if (empty($this->label)) {
			$this->label = Yii::t('common', 'Customer');
		}
		if (!isset($this->filterInputOptions['placeholder'])) {
			$this->filterInputOptions['placeholder'] = Yii::t('common', 'Lastname');
		}
		parent::init();
	}
}
