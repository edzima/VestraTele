<?php

namespace common\modules\reminder\widgets;

use yii\base\Widget;
use yii\data\DataProviderInterface;

class ReminderGridModal extends Widget {

	public string $controller;
	public ?string $createUrl;

	public DataProviderInterface $dataProvider;

	/** @see ReminderGridWidget */
	public $reminderGridOptions = [
		'visibleUserColumn' => false,
	];

	public function run() {
		$gridOptions = array_merge($this->defaultReminderGridOptions(), $this->reminderGridOptions);
		return $this->render('reminder-grid-modal', [
			'dataProvider' => $this->dataProvider,
			'controller' => $this->controller,
			'pjaxId' => $this->generatePjaxId(),
			'createUrl' => $this->createUrl,
			'gridOptions' => $gridOptions,
		]);
	}

	public function defaultReminderGridOptions(): array {
		return [
			'pjax' => true,
			'dataProvider' => $this->dataProvider,
			'visibleUserColumn' => false,
			'pjaxSettings' => [
				'options' => [
					'id' => $this->generatePjaxId(),
				],
			],
			'actionColumn' => [
				'controller' => $this->controller,
				'visibleButtons' => [
					'view' => false,
				],
			],
		];
	}

	private function generatePjaxId(): string {
		return str_replace('/', '-', $this->controller) . '-pjax';
	}
}
