<?php

namespace common\modules\lead\widgets;

use common\helpers\Url;
use common\modules\lead\models\LeadType;
use common\widgets\ButtonDropdown;
use Yii;

class LeadTypeBtnWidget extends ButtonDropdown {

	public string $routeItems = '/lead/type/view';

	public function init(): void {
		parent::init();
		if ($this->label === 'Button') {
			$this->label = Yii::t('lead', 'Lead Type');
		}

		if (!isset($this->dropdown['items'])) {
			$this->dropdown['items'] = $this->defaultItems();
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
				'url' => $this->getItemUrl($id),
			];
		}
		return $items;
	}

	protected function getItemUrl(int $typeId): string {
		return Url::toRoute([$this->routeItems, 'id' => $typeId]);
	}

	protected function getNames(): array {
		return LeadType::getNames();
	}
}
