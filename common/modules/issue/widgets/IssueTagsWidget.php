<?php

namespace common\modules\issue\widgets;

use common\assets\TooltipAsset;
use common\helpers\Html;
use common\models\issue\IssueTag;
use common\models\issue\IssueTagType;
use yii\base\Widget;
use yii\web\View;

class IssueTagsWidget extends Widget {

	public const POSITION_ISSUE_DETAIL_BEFORE = IssueTagType::VIEW_ISSUE_POSITION_BEFORE_DETAILS;
	public const POSITION_ISSUE_DETAIL_AFTER = IssueTagType::VIEW_ISSUE_POSITION_AFTER_DETAILS;

	public ?string $position = null;

	/**
	 * @var IssueTag[]
	 */
	public array $models = [];

	public string $containerTag = 'div';
	public array $containerOptions = [];

	public bool $onlyActive = true;

	public bool $groupByType = true;

	public array $itemOptions = [
		'class' => 'label',
	];
	public string $groupTag = 'div';
	public array $groupTagOptions = [];
	public bool $groupLabel = false;

	public function init() {
		if ($this->position !== null) {
			$this->models = IssueTagType::positionFilter($this->models, $this->position);
		}
		usort($this->models, function (IssueTag $a, IssueTag $b) {
			$typeA = $a->tagType ?: null;
			$typeB = $b->tagType ?: null;
			if ($typeA === null && $typeB !== null) {
				return -1;
			}
			if ($typeB === null && $typeA !== null) {
				return 1;
			}
			if ($typeA === null && $typeB === null) {
				return 0;
			}
			return ($typeA->sort_order < $typeB->sort_order) ? -1 : 1;
		});

		parent::init();
		Html::addCssClass($this->containerOptions, 'tags-wrapper');
		$this->tooltipInit();
	}

	protected function tooltipInit(): void {
		$this->view->on(View::EVENT_END_BODY, function () {
			TooltipAsset::register($this->view);
			$this->view->registerJs(
				TooltipAsset::initScript()
			);
		});
	}

	public function run() {
		$models = $this->models;
		if (empty($models)) {
			return '';
		}
		$content = [];
		$content[] = Html::beginTag($this->containerTag, $this->containerOptions);
		if ($this->groupByType) {
			$content[] = $this->renderGroups($models);
		} else {
			foreach ($models as $model) {
				$content[] = $this->renderTag($model);
			}
		}

		$content[] = Html::endTag($this->containerTag);
		return implode(' ', $content);
	}

	public function renderGroups(array $models): string {
		$groups = $this->typeGroups($models);
		$without = $groups['withoutTypes'];
		$types = $groups['withTypes'];
		$content = [];
		foreach ($types as $group) {
			$content[] = $this->renderGroup($group);
		}
		foreach ($without as $tag) {
			$content[] = $this->renderTag($tag);
		}
		return implode(' ', $content);
	}

	public function renderTag(IssueTag $model, array $options = []): string {
		if (!$this->shouldRenderTag($model)) {
			return '';
		}
		$options = array_merge($options, $this->getItemOptions($model));
		return
			Html::a(
				Html::encode($model->name),
				['issue/index', 'IssueSearch[tagsIds]' => $model->id],
				$options
			);
	}

	protected function shouldRenderTag(IssueTag $model): bool {
		return $this->onlyActive ? $model->is_active : true;
	}

	public function getItemOptions(IssueTag $model): array {
		$options = $this->itemOptions;
		$typeOptions = $model->tagType ? static::tagTypeOptions($model->tagType) : [];
		$tooltipOptions = $this->tooltipOptions($model);
		return array_merge($typeOptions, $tooltipOptions, $this->itemOptions);
	}

	protected function tooltipOptions(IssueTag $model): array {
		$tooltip = $this->renderTagTooltip($model);
		if (empty($tooltip)) {
			return [];
		}
		return [
			TooltipAsset::DEFAULT_ATTRIBUTE_NAME => Html::encode(
				$tooltip
			),
		];
	}

	public static function tagTypeOptions(IssueTagType $type): array {
		$options = [];
		if ($type->css_class) {
			Html::addCssClass($options, $type->css_class);
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
		return $options;
	}

	/**
	 * @param IssueTag[] $models
	 */
	private function typeGroups(array $models): array {
		$types = [];
		$withoutTypes = [];
		foreach ($models as $model) {
			if ($model->tagType) {
				$types[$model->tagType->id][] = $model;
			} else {
				$withoutTypes[] = $model;
			}
		}
		return [
			'withTypes' => $types,
			'withoutTypes' => $withoutTypes,
		];
	}

	/**
	 * @param IssueTag[] $tags
	 * @return void
	 */
	private function renderGroup(array $tags): string {
		if (empty($tags)) {
			return '';
		}
		$content = [];
		$options = $this->groupTagOptions;
		Html::addCssClass($options, 'group-tag-wrapper');
		$content[] = Html::beginTag($this->groupTag, $options);
		if ($this->groupLabel) {
			$type = reset($tags)->tagType;
			if ($type) {
				$content[] = Html::label(Html::encode($type->name));
			}
		}
		foreach ($tags as $tag) {
			$content[] = $this->renderTag($tag);
		}
		$content[] = Html::endTag($this->groupTag);
		return implode(' ', $content);
	}

	public function renderTagTooltip(IssueTag $tag): string {
		$content = [];
		if ($tag->tagType && !$this->groupLabel) {
			$content[] = $tag->tagType->name;
		}
		if ($tag->description) {
			$content[] = $tag->description;
		}
		return implode(': ', $content);
	}

}
