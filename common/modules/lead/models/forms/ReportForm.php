<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\LeadAnswer;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadQuestion;
use common\modules\lead\models\LeadStatus;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class ReportForm extends Model {

	public int $status_id;
	public string $details = '';
	public int $owner_id;

	public $closedQuestions = [];

	private ActiveLead $lead;
	private ?LeadReport $model = null;
	/* @var LeadQuestion[] */
	private ?array $questions = null;
	/* @var LeadAnswer[] */
	private array $answersModels = [];

	public function attributeLabels(): array {
		return [
			'status_id' => Yii::t('lead', 'Status'),
			'details' => Yii::t('lead', 'Details'),
		];
	}

	public function rules(): array {
		return [
			[['!owner_id', 'status_id'], 'required'],
			['details', 'string'],
			[
				'details', 'required',
				'when' => function () {
					return empty($this->closedQuestions);
				},
				'enableClientValidation' => false,
				'message' => Yii::t('lead', 'Details cannot be blank when closed questions is empty.'),
			],
			[
				'closedQuestions', 'required',
				'when' => function () {
					return empty($this->details);
				},
				'enableClientValidation' => false,
				'message' => Yii::t('lead', 'Closed questions must be set when details is blank.'),
			],
			['status_id', 'in', 'range' => array_keys(static::getStatusNames())],
			['closedQuestions', 'in', 'range' => array_keys($this->getClosedQuestionsData()), 'allowArray' => true],
		];
	}

	public function getClosedQuestionsData(): array {
		return ArrayHelper::map($this->getClosedQuestions(), 'id', 'name');
	}

	/**
	 * @return LeadQuestion[]
	 */
	public function getOpenQuestions(): array {
		return array_filter($this->getQuestions(), static function (LeadQuestion $question): bool {
			return $question->hasPlaceholder();
		});
	}

	/**
	 * @return LeadQuestion[]
	 */
	public function getClosedQuestions(): array {
		return array_filter($this->getQuestions(), static function (LeadQuestion $question): bool {
			return !$question->hasPlaceholder();
		});
	}

	/**
	 * @return AnswerForm[]
	 */
	public function getAnswersModels(): array {
		if (empty($this->answersModels)) {
			foreach ($this->getOpenQuestions() as $question) {
				$this->answersModels[$question->id] = $this->getAnswerForm($question);
			}
		}
		return $this->answersModels;
	}

	private function getAnswerForm(LeadQuestion $question): AnswerForm {
		$model = new AnswerForm();
		$answer = $this->getModel()->answers[$question->id];
		if ($answer) {
			$model->setModel($answer);
		}
		$model->setQuestion($question);
		return $model;
	}

	/**
	 * @param int $questionID
	 * @return LeadAnswer
	 * @todo remove after complete use AnswerForm
	 */
	private function getAnswer(int $questionID): LeadAnswer {
		$answer = $this->getModel()->answers[$questionID] ?? null;
		if ($answer === null) {
			$answer = new LeadAnswer();
			$answer->question_id = $questionID;
		}
		return $answer;
	}

	public function getQuestions(): array {
		if ($this->questions === null) {
			$answeredQuestionsIds = $this->lead->getReports()
				->joinWith('answers')
				->select('question_id')
				->column();

			$this->questions = LeadQuestion::find()
				->forStatus($this->status_id)
				->forType($this->getLeadTypeID())
				->andFilterWhere(['not', ['id' => $answeredQuestionsIds]])
				->all();
		}
		return $this->questions;
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}

		$model = $this->getModel();
		$model->details = $this->details;
		$model->old_status_id = $this->lead->getStatusId();
		$model->status_id = $this->status_id;
		$model->owner_id = $this->owner_id;
		$model->lead_id = $this->lead->getId();
		$isNewRecord = $model->isNewRecord;
		$model->save();

		if ($this->status_id !== $this->lead->getStatusId()) {
			$this->lead->updateStatus($this->status_id);
		}
		if (!$isNewRecord) {
			$model->unlinkAll('answers', true);
		}
		foreach ($this->getAnswersModels() as $answer) {
			$answer->report_id = $model->id;
			$answer->save();
		}
		$this->saveClosedQuestions();

		return true;
	}

	private function saveClosedQuestions(): void {
		if (empty($this->closedQuestions)) {
			return;
		}
		$rows = [];
		$report_id = $this->getModel()->id;
		foreach ($this->closedQuestions as $question_id) {
			$rows[] = [
				'report_id' => $report_id,
				'question_id' => $question_id,
			];
		}
		if (!empty($rows)) {
			LeadAnswer::getDb()
				->createCommand()
				->batchInsert(LeadAnswer::tableName(), ['report_id', 'question_id'], $rows)
				->execute();
		}
	}

	public function load($data, $formName = null) {
		return parent::load($data, $formName)
			&& AnswerForm::loadMultiple($this->getAnswersModels(), $data, $formName);
	}

	public function validate($attributeNames = null, $clearErrors = true) {
		return parent::validate($attributeNames, $clearErrors);
		//@todo fix validate answer required report_id
		return parent::validate($attributeNames, $clearErrors)
			&& LeadReportForm::validateMultiple($this->getAnswersModels());
	}

	public function setLead(ActiveLead $lead): void {
		$this->lead = $lead;
		$this->status_id = $lead->getStatusId();
	}

	public function getLead(): ActiveLead {
		return $this->lead;
	}

	public function getModel(): LeadReport {
		if ($this->model === null) {
			$this->model = new LeadReport();
		}
		return $this->model;
	}

	public function getLeadTypeID(): int {
		return $this->lead->getSource()->getType()->getID();
	}

	public static function getStatusNames(): array {
		return LeadStatus::getNames();
	}
}
