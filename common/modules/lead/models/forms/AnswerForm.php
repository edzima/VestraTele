<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\LeadAnswer;
use common\modules\lead\models\LeadQuestion;
use yii\base\Model;

class AnswerForm extends Model {

	public int $question_id;
	public string $answer = '';

	private ?LeadAnswer $model = null;
	private ?LeadQuestion $question = null;

	public function rules(): array {
		return [
			['question_id', 'required'],
			['question_id', 'integer'],
			['answer', 'string'],
			[
				'answer', 'required',
				'when' => function () {
					return $this->getQuestion()->is_required;
				},
				//	'enableClientValidation' => false,
			],
		];
	}

	public function setQuestion(LeadQuestion $question): void {
		$this->question = $question;
		$this->question_id = $question->id;
	}

	public function getQuestion(): ?LeadQuestion {
		if ($this->question === null || $this->question !== $this->question_id) {
			$this->question = LeadQuestion::findOne($this->question_id);
		}
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
		$this->question_id = $model->question_id;
	}

}
