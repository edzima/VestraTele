<?php

namespace common\modules\issue;

use common\assets\TooltipAsset;
use common\helpers\Html;
use common\models\issue\IssueInterface;
use common\models\issue\IssueNote;
use common\models\issue\IssueSearch;
use common\widgets\grid\DataColumn;
use Yii;

class IssueNoteColumn extends DataColumn {

	public $attribute = 'noteFilter';
	public $noWrap = true;
	public bool $contentCenter = true;
	public $format = 'html';
	public bool $tooltip = true;

	public ?string $pinnedContentClass = 'warning';

	public function init(): void {
		parent::init();
		if (empty($this->label)) {
			$this->label = Yii::t('issue', 'Issue Notes');
		}
		if ($this->filter === null && $this->grid->filterModel instanceof IssueSearch) {
			$this->filter = [
				IssueSearch::NOTE_ONLY_PINNED => Yii::t('issue', 'Only Pinned'),
			];
		}
	}

	public function getDataCellValue($model, $key, $index): string {
		assert($model instanceof IssueInterface);
		$issue = $model->getIssueModel();
		if (empty($issue->issueNotes)) {
			return 0;
		}
		$pinned = IssueNote::pinnedNotesFilter($issue->issueNotes);
		if (empty($pinned)) {
			return count($issue->issueNotes);
		}
		$firstPinned = reset($pinned);
		return Html::tag('strong', count($pinned), [
				TooltipAsset::DEFAULT_ATTRIBUTE_NAME => Html::encode($firstPinned->title),
			]) . ' / ' . count($issue->issueNotes);
	}

	protected function fetchContentOptions($model, $key, $index): array {
		assert($model instanceof IssueInterface);
		$options = parent::fetchContentOptions($model, $key, $index);
		$notes = $model->getIssueModel()->issueNotes;
		if (!empty($notes)) {
			$last = reset($notes);
			$options[TooltipAsset::DEFAULT_ATTRIBUTE_NAME] = Html::encode($last->title);
			if (!empty($this->pinnedContentClass) && !empty(IssueNote::pinnedNotesFilter($notes))) {
				Html::addCssClass($options, $this->pinnedContentClass);
			}
		}
		return $options;
	}

}
