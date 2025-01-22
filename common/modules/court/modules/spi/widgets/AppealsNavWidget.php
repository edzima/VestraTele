<?php

namespace common\modules\court\modules\spi\widgets;

use common\helpers\ArrayHelper;
use common\helpers\Url;
use common\modules\court\modules\spi\Module;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\bootstrap\Nav;

class AppealsNavWidget extends Widget {

	public array $items = [];
	public ?string $appealParamName = null;

	public array $navOptions = [
		'class' => Nav::class,
		'options' => [
			'class' => 'nav nav-pills',
		],
	];
	public ?string $activeAppeal = null;
	public bool $getAppealFromModule = true;

	public function init(): void {
		parent::init();

		if ($this->appealParamName === null) {
			$this->appealParamName = Module::getInstance()->appealParamName ?? null;
		}
		if ($this->activeAppeal === null && $this->getAppealFromModule) {
			$this->activeAppeal = Module::getInstance()->getAppeal() ?? null;
		}
		if (empty($this->appealParamName)) {
			throw new InvalidConfigException('appealParamName must be set');
		}

		if (empty($this->items)) {
			$this->items = $this->defaultItems();
		}
	}

	public function run(): string {

		if (empty($this->items)) {
			return '';
		}
		$options = $this->navOptions;
		$class = ArrayHelper::remove($options, 'class', Nav::class);
		$options['items'] = $this->items;
		return $class::widget($options);
	}

	private function defaultItems(): array {
		$appealsNames = Module::getAppealsNames();
		$items = [];
		foreach ($appealsNames as $appeal => $name) {
			$items[] = [
				'url' => Url::current([$this->appealParamName => $appeal]),
				'label' => $name,
				'active' => $appeal == $this->activeAppeal,
			];
		}
		return $items;
	}

}
