<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\LeadAnswer;
use common\modules\lead\models\LeadQuestion;
use yii\base\Model;

class AnswerForm extends Model {

	public ?string $answer = null;

	public $report_id;

	private ?LeadAnswer $model = null;
	private LeadQuestion $question;

	public function __construct(LeadQuestion $question, $config = []) {
		$this->question = $question;
		parent::__construct($config);
	}

	public function rules(): array {
		$rules = [];
		if ($this->getQuestion()->is_required) {
			$rules[] = [
				'answer', 'required',
			];
		}
		if ($this->getQuestion()->isBoolean()) {
			$rules[] = [
				'answer', 'boolean',
			];
		} else {
			$rules[] = [
				'answer', 'string',
			];
			$rules[] = [
				'answer', 'trim',
			];
		}
		if ($this->getQuestion()->isRadioGroup()) {
			$rules[] = [
				'answer', 'in', 'range' => $this->getQuestion()->getRadioValues(),
			];
		}
		$rules[] = [
			'answer', 'default', 'value' => null,
		];

		return $rules;
	}

	public function attributeLabels(): array {
		return [
			'answer' => $this->getQuestion()->name,
		];
	}

	public function getQuestion(): LeadQuestion {
		return $this->question;
	}

	public function getModel(): LeadAnswer {
		if ($this->model === null) {
			$this->model = new LeadAnswer();
		}
		return $this->model;
	}

	public function setModel(LeadAnswer $model): void {
		$this->model = $model;
		$this->answer = $model->answer;
		$this->question = $model->question;
		$this->report_id = $model->report_id;
	}

	public function save(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		if ($this->getQuestion()->isNewRecord) {
			$this->getQuestion()->save();
		}
		$model = $this->getModel();
		$model->answer = $this->answer;
		$model->question_id = $this->getQuestion()->id;
		$model->report_id = $this->report_id;
		if ($this->shouldRemove()) {
			$model->delete();
			return true;
		}
		return $model->save(false);
	}

	protected function shouldRemove(): bool {
		return !$this->getQuestion()->isTag() && ($this->answer === null || $this->answer === '');
	}

	public function getFormId(): string {
		return $this->question->id . '_' . $this->report_id;
	}

	public function hasAnswer(): bool {
		return $this->answer !== null && $this->answer !== '';
	}

}
