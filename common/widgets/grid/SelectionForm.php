<?php

namespace common\widgets\grid;

use common\widgets\ActiveForm;

class SelectionForm extends ActiveForm {

	public string $gridId;
	public string $formWrapperSelector;

	public function run(): string {
		$this->view->registerJs(static::generateScript($this->gridId, $this->formWrapperSelector));
		return parent::run();
	}

	public static function generateScript(string $gridId, string $formWrapperSelector): string {
		$script = <<< JS
		(function () {
			let grid = jQuery('#$gridId');
			let formWrapper = document.querySelector('$formWrapperSelector');
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
		})();
		JS;
		return $script;
	}
}
