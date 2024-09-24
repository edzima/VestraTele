<?php

namespace backend\modules\issue\widgets;

use backend\helpers\Url;
use Closure;
use common\models\issue\IssueInterface;
use common\models\settlement\SettlementType;
use common\widgets\ButtonDropdown;
use Yii;

class IssueCreateSettlementButtonDropdown extends ButtonDropdown {

	public array $route = ['/settlement/calculation/create'];
	public string $issueRouteParam = 'issueId';

	public IssueInterface $issue;
	public string $typeRouteParam = 'typeId';

	public ?Closure $typeUrl = null;

	public function init(): void {
		parent::init();
		if ($this->label === 'Button') {
			$this->label = Yii::t('settlement', 'Create settlement');
		}
		if (!isset($this->dropdown['items'])) {
			$this->dropdown['items'] = $this->defaultItems();
		}
		if (!isset($this->options['href'])) {
			$route = $this->route;
			$route[$this->issueRouteParam] = $this->issue->getIssueId();
			$this->options['href'] = $route;
		}
	}

	protected function defaultItems(): array {
		$settlementTypes = array_filter(SettlementType::getModels(),
			function (SettlementType $type) {
				return $type->is_active
					&& $type->isForIssueTypeId($this->issue->getIssueTypeId());
			});
		$items = [];
		foreach ($settlementTypes as $type) {
			$items[] = $this->settlementTypeItem($type);
		}
		return $items;
	}

	protected function settlementTypeItem(SettlementType $type): array {
		return [
			'label' => $type->name,
			'url' => $this->typeUrl($type),
		];
	}

	protected function typeUrl(SettlementType $type): string {
		if ($this->typeUrl) {
			return call_user_func($this->typeUrl, $type, $this->issue);
		}
		$route = $this->route;
		$route[$this->issueRouteParam] = $this->issue->getIssueId();
		$route[$this->typeRouteParam] = $type->id;
		return Url::to($route);
	}

}
