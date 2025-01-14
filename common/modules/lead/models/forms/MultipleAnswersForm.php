<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\LeadAnswer;
use common\modules\lead\models\LeadQuestion;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class MultipleAnswersForm extends Model {

	public $tags = [];
	public $report_id;

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

	public function __construct(array $answers, $config = []) {
		parent::__construct($config);
		$this->setAnswers($answers);
	}

	public function rules(): array {
		return [
			['tags', 'in', 'range' => array_keys($this->getTagsData()), 'allowArray' => true],
		];
	}

	public function attributeLabels(): array {
		return [
			'tags' => Yii::t('lead', 'Tags'),
		];
	}

	/**
	 * @param LeadAnswer[] $answers
	 * @return void
	 */
	protected function setAnswers(array $answers): void {
		$this->answers = $answers;
		$this->setTags($answers);
	}

	protected function setTags(array $answers): void {
		foreach ($answers as $answer) {
			if ($answer->question->isTag()) {
				$this->tags[] = $answer->question_id;
			}
		}
	}

	public function getTagsData(): array {
		return ArrayHelper::map($this->getTags(), 'id', 'name');
	}

	/**
	 * @return LeadQuestion[]
	 */
	public function getTags(): array {
		return array_filter($this->getQuestions(), static function (LeadQuestion $question): bool {
			return $question->isTag();
		});
	}

	/**
	 * @return AnswerForm[]
	 */
	public function getAnswersModels(): array {
		if (empty($this->answersModels)) {
			foreach ($this->getQuestions() as $question) {
				if (!$question->isTag()) {
					$answerForm = $this->createAnswerForm($question);
					$this->answersModels[$answerForm->getFormId()] = $answerForm;
				}
			}
		}
		return $this->answersModels;
	}

	private function createAnswerForm(LeadQuestion $question): AnswerForm {
		$model = new AnswerForm($question);
		$answer = $this->findAnswer($question->id);
		if ($answer) {
			$model->setModel($answer);
		} else {
			$model->report_id = $this->report_id;
		}
		return $model;
	}

	private function findAnswer(int $questionId): ?LeadAnswer {
		foreach ($this->getAnswers() as $answer) {
			if ($answer->question_id == $questionId) {
				return $answer;
			}
		}
		return null;
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
		$load = parent::load($data, $formName);
		if (!$load) {
			return false;
		}
		$answers = $this->getAnswersModels();
		if (!empty($answers)) {
			return AnswerForm::loadMultiple($answers, $data, $answersFormName);
		}
		return true;
	}

	public function save(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		$models = $this->getAnswersModels();
		foreach ($models as $model) {
			if (empty($model->report_id)) {
				$model->report_id = $this->report_id;
			}
			$model->save(false);
		}
		$this->saveTags();

		return true;
	}

	protected function saveTags(): void {
		$tags = (array) $this->tags;
		if (empty($this->report_id)) {
			foreach ($this->answers as $answer) {
				if ($answer->question->isTag()) {
					if (!in_array($answer->question_id, $tags)) {
						$answer->delete();
					}
				}
			}
		} else {
			$data = [];
			foreach ($tags as $tag) {
				if ($tag) {
					$data[] = [
						'report_id' => $this->report_id,
						'question_id' => $tag,
						'answer' => true,
					];
				}
			}
			if (!empty($data)) {
				LeadAnswer::getDb()->createCommand()
					->batchInsert(LeadAnswer::tableName(),
						[
							'report_id',
							'question_id',
							'answer',
						],
						$data)
					->execute();
			}
		}
	}

	public function hasAnswers(): bool {
		if (!empty($this->tags)) {
			return true;
		}
		foreach ($this->getAnswersModels() as $answer) {
			if ($answer->hasAnswer()) {
				return true;
			}
		}
		return false;
	}

	public function validate($attributeNames = null, $clearErrors = true): bool {
		return parent::validate($attributeNames, $clearErrors)
			&& AnswerForm::validateMultiple($this->getAnswersModels());
	}

}
