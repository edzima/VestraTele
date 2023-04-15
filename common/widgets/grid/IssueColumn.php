<?php

namespace common\widgets\grid;

use common\helpers\Html;
use common\models\issue\IssueInterface;
use common\models\issue\IssueTagType;
use common\modules\issue\widgets\IssueTagsWidget;
use Yii;

class IssueColumn extends DataColumn {

	use IssueTagsColumnTrait;

	public $noWrap = true;

	public $attribute = 'issue_id';

	public array $linkOptions = [
		'data-pjax' => '0',
	];

	public ?string $viewBaseUrl = null;
	public $format = 'html';

	public bool $onlyUserLink = false;

	public function init(): void {
		if (empty($this->label)) {
			$this->label = Yii::t('issue', 'Issue');
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
		if ($this->onlyUserLink && !Yii::$app->user->canSeeIssue($model)) {
			return $model->getIssueName();
		}

		return Html::a($model->getIssueName(),
			[$this->viewBaseUrl, 'id' => $model->getIssueId()],
			$this->linkOptions);
	}

}
