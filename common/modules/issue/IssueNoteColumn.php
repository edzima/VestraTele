<?php

namespace common\modules\issue;

use common\assets\TooltipAsset;
use common\helpers\Html;
use common\models\issue\IssueInterface;
use common\models\issue\IssueNote;
use common\widgets\grid\DataColumn;
use common\widgets\GridView;
use Yii;

class IssueNoteColumn extends DataColumn {

	public $noWrap = true;
	public bool $contentCenter = true;
	public $format = 'html';

	public ?string $pinnedContentClass = 'warning';

	public function init(): void {
		parent::init();
		if (empty($this->label)) {
			$this->label = Yii::t('issue', 'Issue Notes');
		}
		$this->grid->on(GridView::EVENT_AFTER_RUN, function () {
			TooltipAsset::register($this->_view);
			$this->_view->registerJs(
				TooltipAsset::initScript(
					TooltipAsset::defaultSelector('#' . $this->grid->getId())
				)
			);
		});
	}

	public function getDataCellValue($model, $key, $index): string {
		assert($model instanceof IssueInterface);
		$issue = $model->getIssueModel();
		if (empty($issue->issueNotes)) {
			return 0;
		}
		$pinned = static::pinnedNotesFilter($issue->issueNotes);
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
			if (!empty($this->pinnedContentClass) && !empty(static::pinnedNotesFilter($notes))) {
				Html::addCssClass($options, $this->pinnedContentClass);
			}
		}
		return $options;
	}

	public static function pinnedNotesFilter(array $notes): array {
		return array_filter($notes, static function (IssueNote $note) {
			return $note->isPinned();
		});
	}

}
