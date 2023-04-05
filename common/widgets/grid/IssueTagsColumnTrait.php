<?php

namespace common\widgets\grid;

use Closure;
use common\models\issue\IssueTagType;
use common\modules\issue\widgets\IssueTagsWidget;

trait IssueTagsColumnTrait {

	public ?string $tagType = null;
	public ?Closure $tags = null;

	protected function renderTags($model, $key, $index): string {
		if (empty($this->tagType) && $this->tags === null) {
			return '';
		}
		if (is_callable($this->tags)) {
			$models = call_user_func($this->tags, $model, $key, $index);
		} else {
			$models = IssueTagType::issuesGridPositionFilter(
				$model->getIssueModel()->tags,
				$this->tagType
			);
		}
		if (!empty($models)) {
			$this->tooltip = true;
		}
		return IssueTagsWidget::widget([
			'models' => $models,
		]);
	}
}
