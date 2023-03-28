<?php

namespace common\widgets\grid;

use common\models\issue\IssueTagType;
use Yii;

class CustomerDataColumn extends DataColumn {

	use IssueTagsColumnTrait;

	public $noWrap = true;
	public bool $ellipsis = true;
	public bool $contentBold = true;

	public $format = 'raw';

	public $attribute = 'customerLastname';
	public $value = 'issue.customer.fullName';

	public function init(): void {
		if (empty($this->label)) {
			$this->label = Yii::t('common', 'Customer');
		}
		if (!isset($this->filterInputOptions['placeholder'])) {
			$this->filterInputOptions['placeholder'] = Yii::t('common', 'Customer');
		}

		if (empty($this->tagType)) {
			$this->tagType = IssueTagType::ISSUES_GRID_POSITION_COLUMN_CUSTOMER_BOTTOM;
		}
		parent::init();
	}

}
