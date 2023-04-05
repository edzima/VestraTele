<?php

namespace common\widgets\grid;

use common\assets\TooltipAsset;
use common\helpers\Html;
use common\models\issue\IssueInterface;
use common\models\issue\IssueTagType;
use Yii;

class IssueColumn extends DataColumn {

	use IssueTagsColumnTrait;

	public $noWrap = true;

	public $attribute = 'issue_id';

	public array $linkOptions = [
		'data-pjax' => '0',
	];

	public bool $detailsTooltip = true;

	public ?string $viewBaseUrl = null;

	public function init(): void {
		if (empty($this->label)) {
			$this->label = Yii::t('issue', 'Issue');
		}
		if ($this->detailsTooltip) {
			$this->contentOptions = function (IssueInterface $model): array {
				if (!empty($model->getIssueModel()->details)) {
					return [
						TooltipAsset::DEFAULT_ATTRIBUTE_NAME => $model->getIssueModel()->details,
					];
				}
				return [];
			};
		}
		if (!empty($this->viewBaseUrl) && !empty($this->linkOptions)) {
			$this->format = 'raw';
			$this->value = function (IssueInterface $model): string {
				return $this->renderIssueLink($model);
			};
		}
		if (empty($this->value)) {
			$this->value = static function (IssueInterface $model): string {
				return $model->getIssueName();
			};
		}
		if (!isset($this->filterInputOptions['placeholder'])) {
			$this->filterInputOptions['placeholder'] = Yii::t('issue', 'Issue Name');
		}
		if (empty($this->tagType)) {
			$this->tagType = IssueTagType::ISSUES_GRID_POSITION_COLUMN_ISSUE_BOTTOM;
		}
		$this->options['style'] = 'width:100px';
		parent::init();
	}

	public function renderIssueLink(IssueInterface $model): string {
		return Html::a($model->getIssueName(),
			[$this->viewBaseUrl, 'id' => $model->getIssueId()],
			$this->linkOptions);
	}

}
