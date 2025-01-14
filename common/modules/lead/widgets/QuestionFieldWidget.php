<?php

namespace common\modules\lead\widgets;

use common\helpers\Html;
use common\modules\lead\models\LeadQuestion;
use yii\widgets\InputWidget;

class QuestionFieldWidget extends InputWidget {

	public LeadQuestion $question;

	public $options = [
		'class' => 'question-field',
	];

	public function init(): void {
		parent::init();
	}

	public function run(): string {
		switch ($this->question->type) {
			case LeadQuestion::TYPE_TEXT:
				return $this->renderTextInput();
			case LeadQuestion::TYPE_BOOLEAN:
				return $this->renderBooleanInput();
			case LeadQuestion::TYPE_RADIO_GROUP:
				return $this->renderRadioGroupInput();
			case LeadQuestion::TYPE_TEXT_AREA:
				return $this->renderTextArea();
			default:
				return '';
		}
	}

	private function renderTextInput(): string {
		$options = $this->options;
		if (!isset($options['placeholder'])) {
			$options['placeholder'] = $this->question->placeholder;
		}
		if ($this->hasModel()) {
			return Html::activeInput('text', $this->model, $this->attribute, $options);
		}
		return Html::input('text', $this->attribute, $options);
	}

	private function renderTextArea(): string {
		$options = $this->options;
		if (!isset($options['placeholder'])) {
			$options['placeholder'] = $this->question->placeholder;
		}
		if ($this->hasModel()) {
			return Html::activeTextarea($this->model, $this->attribute, $options);
		}
		return Html::textarea($this->attribute, $options);
	}

	private function renderBooleanInput(): string {
		return $this->renderRadioList(Html::booleanDropdownList());
	}

	private function renderRadioList(array $items): string {
		$options = $this->options;
		Html::removeCssClass($options, 'form-control');
		if ($this->hasModel()) {
			return Html::activeRadioList(
				$this->model,
				$this->attribute,
				$items,
				$options
			);
		}
		return Html::radioList(
			$this->attribute,
			$items,
			$options
		);
	}

	private function renderRadioGroupInput(): string {
		$values = $this->question->getRadioValues();
		if (empty($values)) {
			return '';
		}
		$values = array_combine($values, $values);
		return $this->renderRadioList($values);
	}

}
