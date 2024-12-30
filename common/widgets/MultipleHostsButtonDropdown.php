<?php

namespace common\widgets;

use common\components\Host;
use common\helpers\Html;
use Yii;

class MultipleHostsButtonDropdown extends ButtonDropdown {

	/**
	 * @var Host[]
	 */
	public array $hosts = [];
	public $tagName = 'a';
	public $containerOptions = [
		'tag' => 'li',
	];

	public function init(): void {
		parent::init();
		if (empty($this->hosts)) {
			$this->hosts = Yii::$app->multipleHosts->hosts ?? [];
		}
		if ($this->label === 'Button') {
			$this->label = Html::icon('link');
			$this->encodeLabel = false;
		}
		if (!isset($this->dropdown['items'])) {
			$this->dropdown['items'] = $this->defaultItems();
		}
	}

	public function run(): string {
		if (empty($this->dropdown['items'])) {
			return '';
		}
		return parent::run();
	}

	public function defaultItems(): array {
		$items = [];
		foreach ($this->hosts as $host) {
			$item = $this->hostItem($host);
			if (!empty($item)) {
				$items[] = $item;
			}
		}
		return $items;
	}

	public function hostItem(Host $host): array {
		if (empty($host->url)) {
			return [];
		}
		return [
			'label' => $host->name,
			'url' => $host->url,
			'linkOptions' => [
				'target' => '_blank',
			],
		];
	}
}
