<?php

namespace common\modules\reminder\widgets;

use yii\base\Widget;
use yii\data\DataProviderInterface;

class ReminderGridModal extends Widget {

	public string $controller;
	public ?string $createUrl;

	public DataProviderInterface $dataProvider;

	public function run() {
		return $this->render('reminder-grid-modal', [
			'dataProvider' => $this->dataProvider,
			'controller' => $this->controller,
			'pjaxId' => $this->generatePjaxId(),
			'createUrl' => $this->createUrl,
		]);
	}

	private function generatePjaxId(): string {
		return str_replace('/', '-', $this->controller) . '-pjax';
	}
}
