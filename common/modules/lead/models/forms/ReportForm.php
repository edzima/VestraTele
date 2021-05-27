<?php

namespace common\modules\lead\models\forms;

use common\models\Address;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\LeadAddress;
use common\modules\lead\models\LeadAnswer;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadQuestion;
use common\modules\lead\models\LeadStatus;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class ReportForm extends Model {

	public int $status_id;
	public ?string $details = null;
	public int $owner_id;

	public $closedQuestions = [];

	public int $addressType = LeadAddress::TYPE_CUSTOMER;
	public bool $withAddress = false;
	public ?Address $address = null;

	private ActiveLead $lead;
	private ?LeadReport $model = null;
	/* @var LeadQuestion[] */
	private ?array $questions = null;
	/* @var AnswerForm[] */
	private array $answersModels = [];

	public function setOpenAnswers(array $questionsAnswers): void {
		$models = $this->getAnswersModels();
		foreach ($questionsAnswers as $question_id => $answer) {
			$model = $models[$question_id] ?? null;
			if ($model) {
				$model->answer = $answer;
			}
		}
	}

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
			['withAddress', 'boolean'],
			[
				'details', 'required',
				'when' => function () {
					return empty($this->closedQuestions) && !$this->hasOpenAnswer();
				},
				'enableClientValidation' => false,
				'message' => Yii::t('lead', 'Details cannot be blank when answers is empty.'),
			],
			[
				'closedQuestions', 'required',
				'when' => function () {
					return empty($this->details) && !$this->hasOpenAnswer();
				},
				'enableClientValidation' => false,
				'message' => Yii::t('lead', 'Closed questions must be set when details is blank.'),
			],
			['status_id', 'in', 'range' => array_keys(static::getStatusNames())],
			['closedQuestions', 'in', 'range' => array_keys($this->getClosedQuestionsData()), 'allowArray' => true],
		];
	}

	private function hasOpenAnswer(): bool {
		return !empty(array_filter($this->getAnswersModels(), static function (AnswerForm $answerForm): bool {
			return !empty($answerForm->answer);
		}));
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
				if ($this->getModel()->isNewRecord
					|| isset($this->getModel()->answers[$question->id])
				) {
					$this->answersModels[$question->id] = $this->getAnswerForm($question);
				}
			}
		}
		return $this->answersModels;
	}

	private function getAnswerForm(LeadQuestion $question): AnswerForm {
		$model = new AnswerForm($question);
		$answer = $this->getModel()->getAnswer($question->id);
		if ($answer) {
			$model->setModel($answer);
		}
		return $model;
	}

	public function getQuestions(): array {
		if ($this->questions === null) {

			$query = LeadQuestion::find()
				->forStatus($this->status_id)
				->forType($this->getLeadTypeID());
			if ($this->getModel()->isNewRecord) {
				$answeredQuestionsIds = $this->lead
					->getAnswers()
					->select('question_id')
					->column();

				$query->andFilterWhere(['not', ['id' => $answeredQuestionsIds]]);
			}
			$this->questions = $query->all();
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
		$this->linkAnswers(!$isNewRecord);
		$this->saveAddress();

		return true;
	}

	private function linkAnswers(bool $unlink): void {
		$model = $this->getModel();
		if ($unlink) {
			$model->unlinkAll('answers', true);
		}
		foreach ($this->getAnswersModels() as $answer) {
			$answer->linkReport($model, false);
		}
		$this->saveClosedQuestions();
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

	public function load($data, $formName = null, $answersFormName = null, $addressFormName = null): bool {
		return parent::load($data, $formName)
			&& $this->loadAnswers($data, $answersFormName)
			&& $this->loadAddress($data, $addressFormName);
	}

	private function loadAnswers($data, $formName = null): bool {
		$models = $this->getAnswersModels();
		if (empty($models)) {
			return true;
		}
		return AnswerForm::loadMultiple($models, $data, $formName);
	}

	private function loadAddress($data, $formName = null): bool {
		return $this->getAddress()->load($data, $formName);
	}

	public function validate($attributeNames = null, $clearErrors = true): bool {
		return parent::validate($attributeNames, $clearErrors)
		&& AnswerForm::validateMultiple($this->getAnswersModels())
		&& $this->withAddress ? $this->getAddress()->validate() : true;
	}

	public function setLead(ActiveLead $lead): void {
		$this->lead = $lead;
		$this->status_id = $lead->getStatusId();
		$this->address = $lead->addresses[$this->addressType]->address ?? null;
		if ($this->address !== null) {
			$this->withAddress = true;
		}
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

	public function setModel(LeadReport $model): void {
		$this->model = $model;
		$this->setLead($model->lead);
		$this->status_id = $model->status_id;
		$this->owner_id = $model->owner_id;
		$this->details = $model->details;
		foreach ($this->getModel()->answers as $answer) {
			if (isset($this->getClosedQuestionsData()[$answer->question_id])) {
				$this->closedQuestions[] = $answer->question_id;
			}
		}
	}

	public function getAddress(): Address {
		if ($this->address === null) {
			$this->address = new Address();
		}
		return $this->address;
	}

	private function getLeadTypeID(): int {
		return $this->lead->getSource()->getType()->getID();
	}

	public static function getStatusNames(): array {
		return LeadStatus::getNames();
	}

	private function saveAddress(): bool {
		if ($this->withAddress && $this->getAddress()->save()) {
			$address = $model->addresses[$this->addressType] ?? new LeadAddress(['type' => $this->addressType]);
			$address->lead_id = $this->getLead()->id;
			$address->address_id = $this->getAddress()->id;
			return $address->save();
		}
		return true;
	}
}
