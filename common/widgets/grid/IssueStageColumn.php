<?php

namespace common\widgets\grid;

use common\models\issue\IssueInterface;
use common\models\issue\IssueTagType;
use common\models\issue\search\IssueStageSearchable;
use common\widgets\GridView;
use kartik\select2\Select2;
use Yii;

class IssueStageColumn extends DataColumn {

	use IssueTagsColumnTrait;

	public const VALUE_SHORT = 'short';
	public const VALUE_NAME = 'name';
	public const VALUE_NAME_WITH_SHORT = 'name-with-short';

	public string $valueType = self::VALUE_SHORT;
	public bool $contentBold = true;

	public function init(): void {
		if (empty($this->label)) {
			$this->label = Yii::t('issue', 'Stage');
		}
		if (empty($this->value)) {
			$this->value = function (IssueInterface $model): string {
				switch ($this->valueType) {
					case static::VALUE_SHORT:
						return $model->getIssueStage()->short_name;
					case static::VALUE_NAME_WITH_SHORT:
						return $model->getIssueStage()->getNameWithShort();
					case static::VALUE_NAME:
						return $model->getIssueStage()->name;
					default:
						return $model->getIssueModel()->getStageName();
				}
			};
		}
		if (empty($this->filter)) {
			$filterModel = $this->grid->filterModel;
			if ($filterModel instanceof IssueStageSearchable) {
				$this->filter = $filterModel->getIssueStagesNames();
			}
		}
		if (empty($this->filterType)) {
			$this->filterType = GridView::FILTER_SELECT2;
		}

		if ($this->valueType === static::VALUE_SHORT) {
			$this->width = '75px';
		}

		if (empty($this->tagType)) {
			$this->tagType = IssueTagType::ISSUES_GRID_POSITION_COLUMN_STAGE_BOTTOM;
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
