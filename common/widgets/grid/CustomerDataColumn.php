<?php

namespace common\widgets\grid;

use common\models\issue\IssueTagType;
use common\modules\issue\widgets\IssueTagsWidget;
use Yii;

class CustomerDataColumn extends DataColumn {

	public $noWrap = true;
	public bool $ellipsis = true;
	public bool $contentBold = true;

	public $format = 'raw';

	public $attribute = 'customerLastname';
	public $value = 'issue.customer.fullName';
	public bool $tooltip = true;

	public function init(): void {
		if (empty($this->label)) {
			$this->label = Yii::t('common', 'Customer');
		}
		if (!isset($this->filterInputOptions['placeholder'])) {
			$this->filterInputOptions['placeholder'] = Yii::t('common', 'Customer');
		}
		parent::init();
	}

	protected function renderTags($model, $key, $index): string {
		return IssueTagsWidget::widget([
			'models' =>
				IssueTagType::issuesGridPositionFilter(
					$model->getIssueModel()->tags,
					IssueTagType::ISSUES_GRID_POSITION_COLUMN_CUSTOMER_BOTTOM
				),
		]);
	}
}
