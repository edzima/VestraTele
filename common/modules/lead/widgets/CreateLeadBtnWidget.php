<?php

namespace common\modules\lead\widgets;

use common\helpers\Url;
use common\modules\lead\models\LeadSource;
use Yii;
use yii\bootstrap\ButtonDropdown;

class CreateLeadBtnWidget extends ButtonDropdown {

	public ?int $owner_id = null;

	public string $baseRoute = '/lead/lead/create';
	public string $routeItems = '/lead/lead/create-from-source';
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

		if (!isset($this->dropdown['items'])) {
			$this->dropdown['items'] = $this->defaultItems();
		}
	}

	protected function defaultItems(): array {
		$names = $this->getNames();
		$items = [];
		foreach ($names as $id => $name) {
			$items[] = [
				'label' => $name,
				'url' => Url::toRoute([$this->routeItems, 'id' => $id, 'phone' => $this->phone]),
			];
		}
		return $items;
	}

	protected function getNames(): array {
		return LeadSource::getNames($this->owner_id);
	}
}
