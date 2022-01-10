<?php

namespace common\widgets\grid;

use common\helpers\Html;
use yii\base\Widget;

class SelectionForm extends Widget {

	public string $id = 'selection-form';
	public string $gridId;
	public string $formWrapperSelector;
	public string $action = '';
	private string $method = 'POST';
	private array $options = [
		'data-pjax' => '',
	];

	public function init(): void {
		parent::init();
		$options = $this->options;
		$options['id'] = $this->getId();
		echo Html::beginForm($this->action, $this->method, $options);
	}

	public function run(): string {
		parent::run();
		$this->view->registerJs($this->getJsScript());
		return Html::endForm();
	}

	private function getJsScript(): string {

		$gridId = $this->gridId;
		$formWrapperSelector = $this->formWrapperSelector;
		$script = <<< JS
		const grid = jQuery('#$gridId');
		const formWrapper = document.querySelector('$formWrapperSelector');
		grid.find("input[type='checkbox']").on('click', function () {
			setTimeout(function () {
				const selected = grid.yiiGridView('getSelectedRows');
				if (selected.length) {
					formWrapper.classList . remove('hidden');
				} else {
					formWrapper.classList . add('hidden');
				}
			}, 100);
		});
		JS;
		return $script;
	}
}
