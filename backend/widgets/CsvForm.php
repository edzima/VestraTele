<?php

namespace backend\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class CsvForm extends Widget {

	public const BUTTON_NAME = 'csv-export';

	public $action = '';
	public $method = 'post';
	public $formOptions = [];
	public $buttonText = 'CSV export';
	public $buttonOptions = [
		'class' => 'btn btn-secondary',
	];

	public function run(): string {
		$formOptions = $this->formOptions;
		Html::addCssClass($formOptions, 'csv-form');
		$content = Html::beginForm($this->action, $this->method, $formOptions);
		$options = $this->buttonOptions;
		$options['name'] = static::BUTTON_NAME;
		$options['title'] = Yii::t('common', 'Export');
		$options['aria-label'] = Yii::t('common', 'Export');
		$content .= Html::submitButton($this->buttonText, $options);
		$content .= Html::endForm();
		return $content;
	}
}
