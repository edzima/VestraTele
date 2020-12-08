<?php

namespace common\widgets\grid;

use common\models\issue\IssueInterface;
use common\models\issue\IssueType;
use common\widgets\GridView;
use kartik\select2\Select2;
use Yii;

class IssueTypeColumn extends DataColumn {

	public function init(): void {
		if (empty($this->label)) {
			$this->label = Yii::t('common', 'Type');
		}
		if (empty($this->value)) {
			$this->value = static function (IssueInterface $model): string {
				return $model->getIssueType()->name;
			};
		}
		if (empty($this->filter)) {
			$this->filter = static::defaultFilter();
		}
		if (empty($this->filterType)) {
			$this->filterType = GridView::FILTER_SELECT2;
		}
		if (empty($this->filterWidgetOptions)) {
			$this->filterWidgetOptions = [
				'options' => [
					'multiple' => true,
					'placeholder' => $this->label,
				],
				'size' => Select2::SIZE_SMALL,
				'showToggleAll' => false,
			];
		}

		$this->options['style'] = 'width:260px';

		parent::init();
	}

	public static function defaultFilter(): array {
		return IssueType::getShortTypesNames();
	}
}
