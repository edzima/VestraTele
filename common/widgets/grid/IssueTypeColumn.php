<?php

namespace common\widgets\grid;

use common\helpers\Html;
use common\models\issue\IssueInterface;
use common\models\issue\IssueTagType;
use common\models\issue\search\IssueTypeSearch;
use common\widgets\GridView;
use kartik\select2\Select2;
use Yii;

class IssueTypeColumn extends DataColumn {

	use IssueTagsColumnTrait;

	public const VALUE_SHORT = 'short';
	public const VALUE_NAME = 'name';
	public const VALUE_NAME_WITH_SHORT = 'name-with-short';

	public string $valueType = self::VALUE_SHORT;
	public bool $contentBold = true;
	public bool $withAdditionalDateAt = false;

	public function getTypeValue(IssueInterface $model): string {
		switch ($this->valueType) {
			case static::VALUE_SHORT:
				return $model->getIssueType()->short_name;
			case static::VALUE_NAME_WITH_SHORT:
				return $model->getIssueType()->getNameWithShort();
			default:
				return $model->getIssueType()->name;
		}
	}

	public function init(): void {
		if (empty($this->label)) {
			$this->label = Yii::t('common', 'Type');
		}
		if (empty($this->value)) {
			$this->value = function (IssueInterface $model): string {
				$value = $this->getTypeValue($model);
				if ($this->withAdditionalDateAt && !empty($model->getIssueModel()->type_additional_date_at)) {
					$this->format = 'html';
					$value = Html::encode($value);
					$value .= '<br><strong>' . Yii::$app->formatter->asDate($model->getIssueModel()->type_additional_date_at) . '</strong>';
				}
				return $value;
			};
		}
		if (empty($this->filter)) {
			$filterModel = $this->grid->filterModel;
			if ($filterModel instanceof IssueTypeSearch) {
				$this->filter = $filterModel->getIssueTypesNames();
				if (count($this->filter) === 1) {
					$this->visible = false;
				}
			}
		}
		if (empty($this->filterType)) {
			$this->filterType = GridView::FILTER_SELECT2;
		}

		if (empty($this->tagType)) {
			$this->tagType = IssueTagType::ISSUES_GRID_POSITION_COLUMN_TYPE_BOTTOM;
		}

//		switch ($this->valueType) {
//			case self::VALUE_NAME_WITH_SHORT:
//				$this->width = '200px';
//				break;
//			case self::VALUE_SHORT:
//				$this->width = '75px';
//				break;
//			case self::VALUE_NAME:
//				$this->width = '250px';
//				break;
//		}
		if ($this->valueType === static::VALUE_SHORT) {
			$this->width = '75px';
		}

		if (empty($this->filterWidgetOptions)) {
			$this->filterWidgetOptions = [
				'options' => [
					'multiple' => true,
					'placeholder' => $this->label,
				],
				'pluginOptions' => [
					'dropdownAutoWidth' => true,
				],
				'size' => Select2::SIZE_SMALL,
				'showToggleAll' => false,
			];
		}

		parent::init();
	}

}
