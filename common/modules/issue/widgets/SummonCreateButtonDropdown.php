<?php

namespace common\modules\issue\widgets;

use backend\helpers\Url;
use common\models\issue\SummonType;
use Yii;
use yii\bootstrap\ButtonDropdown;

class SummonCreateButtonDropdown extends ButtonDropdown {

	public string $route;
	public string $issueViewRoute;

	public ?string $returnUrl = null;
	public $options = [
		'class' => 'btn-warning',
	];
	public $tagName = 'a';
	public $split = true;

	public ?int $issueId = null;

	public function init(): void {
		parent::init();
		if ($this->label === 'Button') {
			$this->label = Yii::t('issue', 'Create Summon');
		}
		if ($this->issueId !== null && $this->returnUrl === null) {
			$this->returnUrl = Url::to([$this->issueViewRoute, 'id' => $this->issueId]);
		}
		if (!isset($this->dropdown['items'])) {
			$this->dropdown['items'] = $this->defaultItems();
		}
		if (!isset($this->options['href'])) {
			$this->options['href'] = [
				$this->route,
				'issueId' => $this->issueId,
				'returnUrl' => $this->returnUrl,
			];
		}
	}

	public function defaultItems(): array {
		$types = SummonType::getNames();
		$items = [];
		foreach ($types as $id => $name) {
			$items[] = [
				'label' => $name,
				'linkOptions' => [
					'data-pjax' => 0,
				],
				'url' => Url::to([
					$this->route,
					'issueId' => $this->issueId,
					'typeId' => $id,
					'returnUrl' => $this->returnUrl,
				]),
			];
		}
		return $items;
	}
}
