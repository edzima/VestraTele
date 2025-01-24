<?php

namespace common\modules\court\modules\spi\widgets;

use common\helpers\ArrayHelper;
use common\helpers\Url;
use common\modules\court\modules\spi\Module;
use common\modules\court\modules\spi\repository\NotificationsRepository;
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

	public bool $withUnreadCount = true;

	public ?NotificationsRepository $notificationsRepository = null;

	public ?Module $module = null;

	public function init(): void {
		parent::init();
		if ($this->module === null) {
			$this->module = Module::getInstance();
		}

		if ($this->appealParamName === null && $this->module) {
			$this->appealParamName = $this->module->appealParamName;
		}
		if ($this->activeAppeal === null && $this->module) {
			$this->activeAppeal = $this->module->getAppeal();
		}
		if ($this->withUnreadCount && $this->notificationsRepository === null) {
			if ($this->module === null) {
				throw new InvalidConfigException('Module must be set when use unread counts.');
			}
			$this->notificationsRepository = $this->module
				->getRepositoryManager()
				->getNotifications();
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
				'label' => $this->getDefaultLabel($appeal, $name),
				'active' => $appeal == $this->activeAppeal,
			];
		}
		return $items;
	}

	protected function getDefaultLabel(string $appeal, string $name): string {
		if (!$this->withUnreadCount) {
			return $name;
		}
		$count = 0;
		if ($this->notificationsRepository) {
			$count = $this->notificationsRepository->getUnread($appeal, $appeal !== $this->activeAppeal);
		}
		$label = $name;
		if ($count) {
			$label .= ' (' . $count . ')';
		}
		return $label;
	}

}
