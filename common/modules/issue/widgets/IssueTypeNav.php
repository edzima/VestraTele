<?php

namespace common\modules\issue\widgets;

use common\behaviors\IssueTypeParentIdAction;
use common\helpers\ArrayHelper;
use common\helpers\Html;
use common\helpers\Url;
use common\models\issue\IssueType;
use Yii;
use yii\base\Widget;
use yii\bootstrap\Nav;

class IssueTypeNav extends Widget {

	public ?IssueType $activeType = null;

	public bool $activeTypeFromId = true;

	/**
	 * @var IssueType[]|null
	 */
	public ?array $models = null;

	public ?int $activeTypeId = null;

	//NAV
	public array $navOptions = [
		'options' => [
			'class' => 'nav nav-pills',
		],
	];
	public array $itemOptions = [
		'encode' => false,
	];

	public ?string $childsClass = null;

	// favorite
	public bool $withFavorite = true;
	public ?int $favoriteId = null;
	public array $favoriteOptions = [
		'data-method' => 'POST',
		'class' => 'favorite-link',
	];

	public bool $onlyUserIssues = false;

	public array $route = [Url::ROUTE_ISSUE_INDEX];
	public string $typeIdParamName = Url::PARAM_ISSUE_PARENT_TYPE;

	public int $allParamValue = IssueTypeParentIdAction::ISSUE_PARENT_TYPE_ALL;

	public array $params = [];
	public bool $withAllItem = true;
	public ?string $allLabel = null;
	public ?int $userId = null;

	public function init(): void {
		parent::init();

		if ($this->activeTypeId === null) {
			$this->activeTypeId = $this->defaultActiveTypeId();
		}
		if ($this->userId === null) {
			$this->userId = $this->defaultUserId();
		}
		if ($this->activeTypeFromId && $this->activeTypeId !== null) {
			$this->activeType = IssueType::get($this->getTypeFromQueryParams());
			if ($this->activeType && $this->activeType->parent) {
				$this->models = $this->activeType->parent->childs;
				$this->allLabel = $this->activeType->parent->name;
				$this->allParamValue = $this->activeType->parent_id;
			}
		}

		if ($this->allLabel === null) {
			$this->allLabel = Yii::t('issue', 'All Issues');
		}

		if ($this->models === null) {
			$this->models = $this->defaultModels();
		}
		if ($this->withFavorite && $this->favoriteId === null) {
			$this->favoriteId = $this->defaultFavoriteId();
		}
	}

	public function run(): string {
		$items = $this->getItems();
		if (empty($items)) {
			return '';
		}
		if ($this->withFavorite) {
			Html::addCssClass($this->navOptions['options'], 'nav-with-favorite');
		}
		$options = $this->navOptions;
		$options['items'] = $items;
		return Nav::widget($options);
	}

	public function getItems(): array {
		$items = [];
		foreach ($this->models as $model) {
			if ($this->beforeItem($model)) {
				$items[] = $this->itemOptions($model);
			}
		}
		if (!empty($items) && $this->withAllItem()) {
			$items[] = $this->allItemOptions();
		}
		return $items;
	}

	protected function withAllItem(): bool {
		return $this->withAllItem;
	}

	protected function allItemOptions(): array {
		$options = $this->itemOptions;
		if (!isset($options['url'])) {
			$options['url'] = $this->getAllUrl();
		}
		if (!isset($options['label'])) {
			$options['label'] = $this->allLabel;
		}
		if (!isset($options['active'])) {
			$options['active'] = $this->isAllActive();
		}

		return $options;
	}

	protected function isAllActive(): bool {
		$queryParam = $this->getTypeFromQueryParams();
		return $queryParam === $this->allParamValue || empty($queryParam);
	}

	protected function getAllUrl(): string {
		$url = $this->route;
		$url[$this->typeIdParamName] = $this->allParamValue;
		return Url::to($url);
	}

	public function itemOptions(IssueType $model, array $options = []): array {
		$options = array_merge($this->itemOptions, $options);
		if (!isset($options['label'])) {
			$options['label'] = $this->getLabel($model);
		}
		if (!isset($options['url'])) {
			$options['url'] = $this->getUrl($model);
		}
		if (!isset($options['visible'])) {
			$options['visible'] = $this->isVisible($model, true);
		}
		if (!isset($options['items'])) {
			$subItemsOptions = ArrayHelper::remove($options, 'subItemsOptions', []);
			$items = $this->getSubItems($model, $subItemsOptions);

			if (!empty($items)) {
				if ($this->childsClass !== null) {
					Html::addCssClass($options, $this->childsClass);
				}
				$options['items'] = $items;
			}
		}
		if (!isset($options['active'])) {
			$options['active'] = $this->isActive($model);
		}

		return $options;
	}

	protected function getLabel(IssueType $model): string {
		$label = $model->name;
		if ($this->withFavorite) {
			$label .= $this->renderFavoriteLink($model);
		}
		return $label;
	}

	protected function renderFavoriteLink(IssueType $model): ?string {
		if (!$this->withFavorite) {
			return '';
		}
		$options = $this->favoriteOptions;
		if ($this->isFavorite($model)) {
			Html::addCssClass($options, 'active');
		}
		$icon = ArrayHelper::remove($options, 'icon', Html::icon('star'));

		return Html::a(
			$icon,
			[
				'/user-settings/favorite-issue-type',
				'type_id' => !$this->isFavorite($model) ? $model->id : null,
				'returnUrl' => Url::current(),
			],
			$options
		);
	}

	protected function isActive(IssueType $model): ?bool {
		if ($this->activeTypeId === $model->id) {
			return true;
		}
		if ($this->activeType) {
			return $this->activeType->id === $model->id;
		}
		$childs = $model->childs;
		foreach ($childs as $child) {
			if ($this->isActive($child)) {
				return true;
			}
		}
		return false;
	}

	protected function isFavorite(IssueType $model): bool {
		return $model->id === $this->favoriteId;
	}

	public function getUrl(IssueType $model, bool $schema = false): ?string {
		$url = $this->route;
		$url[$this->typeIdParamName] = $model->id;
		return Url::to($url, $schema);
	}

	protected function defaultFavoriteId(): ?int {
		return Yii::$app->user->getFavoriteIssueType();
	}

	protected function defaultModels(): array {
		return IssueType::getMainTypes();
	}

	protected function defaultActiveTypeId(): ?int {
		return $this->getTypeFromQueryParams();
	}

	protected function getTypeFromQueryParams(): ?int {
		return Yii::$app->request->getQueryParams()[$this->typeIdParamName] ?? null;
	}

	protected function getSubItems(IssueType $model, array $options = []): array {
		$models = $model->childs;
		$items = [];
		foreach ($models as $model) {
			if ($this->beforeItem($model)) {
				$items[] = $this->itemOptions($model, $options);
			}
		}
		return $items;
	}

	protected function beforeItem(IssueType $model): bool {
		return true;
	}

	protected function defaultUserId(): ?int {
		return Yii::$app->user->getId();
	}

	private function isVisible(IssueType $model, bool $withChildren): bool {
		if ($this->userId) {
			return $model->isForUser($this->userId, $this->onlyUserIssues, $withChildren);
		}
		return true;
	}

}
