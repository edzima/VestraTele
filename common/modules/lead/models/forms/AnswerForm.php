<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\LeadAnswer;
use common\modules\lead\models\LeadQuestion;
use common\modules\lead\models\LeadReport;
use yii\base\Model;

class AnswerForm extends Model {

	public ?string $answer = null;

	private ?LeadAnswer $model = null;
	private LeadQuestion $question;

	public function __construct(LeadQuestion $question, $config = []) {
		$this->question = $question;
		parent::__construct($config);
	}

	public function rules(): array {
		return [
			['answer', 'string'],
			[
				'answer', 'required',
				'when' => function () {
					return $this->getQuestion()->is_required;
				},
				'enableClientValidation' => false,
			],
		];
	}

	public function attributeLabels(): array {
		return [
			'answer' => $this->getQuestion()->name,
		];
	}

	public function getQuestion(): LeadQuestion {
		return $this->question;
	}

	private function getModel(): LeadAnswer {
		if ($this->model === null) {
			$this->model = new LeadAnswer();
		}
		return $this->model;
	}

	public function setModel(LeadAnswer $model): void {
		$this->model = $model;
		$this->answer = $model->answer;
	}

	public function linkReport(LeadReport $report, bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		$questionId = $this->getQuestion()->id;
		$model = $report->getAnswer($questionId) ?? $this->getModel();
		if (empty($this->answer) && $this->getQuestion()->hasPlaceholder()) {
			if (!$model->isNewRecord) {
				$model->delete();
			}
			return false;
		}
		$model->answer = $this->answer;
		$model->question_id = $questionId;
		$report->link('answers', $model);
		return true;
	}

}
