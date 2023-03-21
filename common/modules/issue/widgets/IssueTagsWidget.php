<?php

namespace common\modules\issue\widgets;

use common\helpers\Html;
use common\models\issue\IssueTag;
use yii\base\Widget;

class IssueTagsWidget extends Widget {

	public string $containerTag = 'span';
	public array $containerOptions = [
		'class' => 'badges-wrapper',
	];

	public bool $onlyActive = true;

	public array $models = [];

	public array $itemOptions = [
		'class' => 'label',
	];

	public function run() {
		$models = $this->models;
		if (empty($models)) {
			return '';
		}
		$content = [];
		$content[] = Html::beginTag($this->containerTag, $this->containerOptions);
		foreach ($models as $model) {
			$content[] = $this->renderTag($model);
		}
		$content[] = Html::endTag($this->containerTag);
		return implode(' ', $content);
	}

	public function renderTag(IssueTag $model): string {
		if (!$this->shouldRenderTag($model)) {
			return '';
		}
		return
			Html::a(
				Html::encode($model->name),
				['index', 'IssueSearch[tagsIds]' => $model->id],
				$this->getItemOptions($model)
			);
	}

	protected function shouldRenderTag(IssueTag $model): bool {
		return $this->onlyActive ? $model->is_active : true;
	}

	public function getItemOptions(IssueTag $model): array {
		$options = $this->itemOptions;
		$type = $model->tagType;
		if ($type) {
			if ($type->css_class) {
				Html::addCssClass($options, $model->tagType->css_class);
			}
			$style = [];
			if ($type->background) {
				$style['background'] = $type->background;
			}
			if ($type->color) {
				$style['color'] = $type->color . ' !important';
			}
			if (!empty($style)) {
				Html::addCssStyle($options, $style);
			}
		}
		return $options;
	}
}
