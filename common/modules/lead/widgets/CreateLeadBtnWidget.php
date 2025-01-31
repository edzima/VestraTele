<?php

namespace common\modules\lead\widgets;

use common\helpers\Url;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadType;
use Yii;
use yii\bootstrap\ButtonDropdown;

class CreateLeadBtnWidget extends ButtonDropdown {

	public ?int $owner_id = null;

	public string $itemsTypes = self::ITEMS_TYPE;
	public const  ITEMS_TYPE = 'type';
	public const ITEMS_SOURCE = 'source';

	public string $baseRoute = '/lead/lead/create';
	public ?string $routeItems = null;
	public ?string $phone = null;

	public $tagName = 'a';
	public $split = true;
	public $options = [
		'class' => 'btn btn-success',
	];

	public function init() {
		parent::init();
		if ($this->label === 'Button') {
			$this->label = Yii::t('lead', 'Create Lead');
		}
		if (!isset($this->options['href'])) {
			$this->options['href'] = Url::toRoute([$this->baseRoute, 'phone' => $this->phone]);
		}

		if ($this->routeItems === null) {
			$this->routeItems = $this->defaultRoute();
		}

		if (!isset($this->dropdown['items'])) {
			$this->dropdown['items'] = $this->defaultItems();
		}
	}

	protected function defaultItems(): array {
		$names = $this->getDefaultItemsNames();
		$items = [];
		foreach ($names as $id => $name) {
			$items[] = [
				'label' => $name,
				'url' => Url::toRoute([$this->routeItems, 'id' => $id, 'phone' => $this->phone]),
			];
		}
		return $items;
	}

	protected function getDefaultItemsNames(): array {
		switch ($this->itemsTypes) {
			case self::ITEMS_TYPE:
				return $this->getTypesNames();
			case self::ITEMS_SOURCE:
				return $this->getSourcesNames();
		}
		return [];
	}

	private function defaultRoute(): ?string {
		switch ($this->itemsTypes) {
			case self::ITEMS_TYPE:
				return '/lead/lead/create-from-type';
			case self::ITEMS_SOURCE:
				return '/lead/lead/create-from-source';
		}
		return null;
	}

	protected function getSourcesNames(): array {
		return LeadSource::getNames(
			$this->owner_id,
			true,
			null,
			true
		);
	}

	protected function getTypesNames(): array {
		return LeadType::getNames();
	}

}
