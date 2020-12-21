<?php

namespace common\widgets\grid;

use common\models\issue\IssueInterface;
use common\models\issue\search\IssueTypeSearch;
use common\widgets\GridView;
use kartik\select2\Select2;
use Yii;

class IssueTypeColumn extends DataColumn {

	public const VALUE_SHORT = 'short';
	public const VALUE_NAME = 'name';
	public const VALUE_NAME_WITH_SHORT = 'name-with-short';

	public $valueType = self::VALUE_SHORT;
	public bool $contentBold = true;

	public function init(): void {
		if (empty($this->label)) {
			$this->label = Yii::t('common', 'Type');
		}
		if (empty($this->value)) {
			$this->value = function (IssueInterface $model): string {
				switch ($this->valueType) {
					case static::VALUE_SHORT:
						return $model->getIssueType()->short_name;
					case static::VALUE_NAME_WITH_SHORT:
						return $model->getIssueType()->getNameWithShort();
					default:
						return $model->getIssueType()->name;
				}
			};
		}
		if (empty($this->filter)) {
			$filterModel = $this->grid->filterModel;
			if ($filterModel instanceof IssueTypeSearch) {
				$this->filter = $filterModel::getIssueTypesNames();
			}
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

		switch ($this->valueType){
			case self::VALUE_NAME_WITH_SHORT:
				$this->width = '200px';
				break;
			case self::VALUE_SHORT:
				$this->width = '50px';
				break;
			case self::VALUE_NAME:
				$this->width = '250px';
				break;
		}

		parent::init();
	}

}
