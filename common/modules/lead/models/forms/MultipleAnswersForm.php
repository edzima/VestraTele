<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\LeadAnswer;
use common\modules\lead\models\LeadQuestion;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class MultipleAnswersForm extends Model {

	public $closedQuestions = [];

	/**
	 * @var array|LeadQuestion[]
	 */
	public ?array $questions = null;

	/* @var AnswerForm[] */
	private array $answersModels = [];

	/**
	 * @var LeadAnswer[]
	 */
	private array $answers = [];

	public function rules(): array {
		return [
			['closedQuestions', 'in', 'range' => array_keys($this->getClosedQuestionsData()), 'allowArray' => true],

		];
	}

	public function attributeLabels(): array {
		return [
			'closedQuestions' => Yii::t('lead', 'Closed Questions'),
		];
	}

	public function __construct(array $answers, $config = []) {
		parent::__construct($config);
		$this->setAnswers($answers);
	}

	/**
	 * @param LeadAnswer[] $answers
	 * @return void
	 */
	protected function setAnswers(array $answers): void {
		$this->answers = $answers;
		foreach ($answers as $answer) {
			if ($answer->question->isClosed()) {
				$this->closedQuestions[] = $answer->question_id;
			}
		}
	}

	public function getClosedQuestionsData(): array {
		return ArrayHelper::map($this->getClosedQuestions(), 'id', 'name');
	}

	/**
	 * @return LeadQuestion[]
	 */
	public function getOpenQuestions(): array {
		return array_filter($this->getQuestions(), static function (LeadQuestion $question): bool {
			return !$question->isClosed();
		});
	}

	/**
	 * @return LeadQuestion[]
	 */
	public function getClosedQuestions(): array {
		return array_filter($this->getQuestions(), static function (LeadQuestion $question): bool {
			return $question->isClosed();
		});
	}

	/**
	 * @return AnswerForm[]
	 */
	public function getAnswersModels(): array {
		if (empty($this->answersModels)) {
			foreach ($this->getAnswers() as $answer) {
				if (!$answer->question->isClosed()) {
					$this->answersModels[$answer->report_id . '.' . $answer->question_id] = $this->createAnswerForm($answer);
				}
			}
		}
		return $this->answersModels;
	}

	private function createAnswerForm(LeadAnswer $answer): AnswerForm {
		$model = new AnswerForm($answer->question);
		$model->setModel($answer);
		return $model;
	}

	/**
	 * @return LeadQuestion[]
	 */
	public function getQuestions(): array {
		if ($this->questions === null) {
			$this->questions = [];
			foreach ($this->getAnswers() as $answer) {
				$this->questions[] = $answer->question;
			}
			LeadQuestion::sortByOrder($this->questions);
		}
		return $this->questions;
	}

	/**
	 * @return LeadAnswer[]
	 */
	private function getAnswers(): array {
		return $this->answers;
	}

	public function load($data, $formName = null, $answersFormName = null): bool {
		return parent::load($data, $formName)
			&& AnswerForm::loadMultiple($this->getAnswersModels(), $data, $answersFormName);
	}

	public function save(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		$models = $this->answersModels;
		foreach ($models as $model) {
			$model->save(false);
		}
		$this->updateClosedQuestions();

		return true;
	}

	protected function updateClosedQuestions(): void {
		$closedQuestions = (array) $this->closedQuestions;
		foreach ($this->answers as $answer) {
			if ($answer->question->isClosed()) {
				if (!in_array($answer->question_id, $closedQuestions)) {
					$answer->delete();
				}
			}
		}
	}

	public function validate($attributeNames = null, $clearErrors = true): bool {
		return parent::validate($attributeNames, $clearErrors)
			&& AnswerForm::validateMultiple($this->getAnswersModels());
	}

}
