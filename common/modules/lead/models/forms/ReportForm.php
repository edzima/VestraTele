<?php

namespace common\modules\lead\models\forms;

use common\models\Address;
use common\models\user\User;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\LeadAddress;
use common\modules\lead\models\LeadAnswer;
use common\modules\lead\models\LeadQuestion;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadUser;
use common\modules\lead\Module;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * ReportForm Class.
 *
 * @property-read array $closedQuestionsData
 * @property-write array $openAnswers
 * @property-read LeadQuestion[] $openQuestions
 */
class ReportForm extends Model {

	public string $leadName = '';
	public int $status_id;
	public ?string $details = null;
	public int $owner_id;
	public bool $withSameContacts = false;
	public bool $is_pinned = false;

	public $closedQuestions = [];

	public int $addressType = LeadAddress::TYPE_CUSTOMER;
	public bool $withAddress = false;
	public ?Address $address = null;
	public bool $withLinkUsers = true;

	private ?ActiveLead $lead = null;
	private ?LeadReport $model = null;
	/* @var LeadQuestion[] */
	private ?array $questions = null;
	/* @var AnswerForm[] */
	private array $answersModels = [];

	public bool $withAnswers = true;
	public int $lead_type_id;

	public $tele_id;
	public $partner_id;

	public $linkUserTypeForLeadNotForReportOwner = LeadUser::TYPE_TELE;

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
			'withAddress' => Yii::t('lead', 'With Address'),
			'withSameContacts' => Yii::t('lead', 'With Same Contacts'),
			'closedQuestions' => Yii::t('lead', 'Closed Questions'),
			'leadName' => Yii::t('lead', 'Lead Name'),
			'is_pinned' => Yii::t('lead', 'Is pinned'),
			'tele_id' => LeadUser::getTypesNames()[LeadUser::TYPE_TELE],
			'partner_id' => LeadUser::getTypesNames()[LeadUser::TYPE_PARTNER],
		];
	}

	public function rules(): array {
		return [
			[['!owner_id', 'status_id'], 'required'],
			[
				'withSameContacts', 'required', 'when' => function (): bool {
				return $this->getModel()->isNewRecord;
			},
			],
			[['details', 'leadName'], 'string'],
			[['details', 'leadName'], 'trim'],
			[['withAddress', 'withSameContacts', 'is_pinned'], 'boolean'],
			[
				'details', 'required',
				'when' => function () {
					return $this->detailsIsRequired();
				},
				'enableClientValidation' => false,
				'message' => Yii::t('lead', 'Details cannot be blank when answers is empty.'),
			],
			[['partner_id', 'tele_id'], 'default', 'value' => null],
			['status_id', 'in', 'range' => array_keys(static::getStatusNames())],
			['closedQuestions', 'in', 'range' => array_keys($this->getClosedQuestionsData()), 'allowArray' => true],
			[
				'partner_id', 'in', 'range' => array_keys($this->getUsersNames()), 'when' => function (): bool {
				return $this->withLinkUsers;
			},
			],
			[
				'tele_id', 'in', 'range' => array_keys($this->getTeleUsersNames()), 'when' => function (): bool {
				return $this->withLinkUsers;
			},
			],
		];
	}

	public function getUsersNames(): array {
		return Module::userNames();
	}

	protected function detailsIsRequired(): bool {
		if (empty($this->getQuestions())) {
			return true;
		}
		return empty($this->closedQuestions) && $this->allOpenAnswersIsEmpty();
	}

	/**
	 * @return ActiveLead[]
	 */
	public function getSameContacts(): array {
		return $this->getLead()->getSameContacts(true);
	}

	private function allOpenAnswersIsEmpty(): ?bool {
		$models = $this->getAnswersModels();
		if (empty($models)) {
			return null;
		}
		$count = count(array_filter($models, static function (AnswerForm $answerForm): bool {
			$answerForm->validate();
			return $answerForm->answer === null;
		}));
		return $count === count($models);
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
	 * @return LeadQuestion[]
	 */
	public function getBooleanQuestions(): array {
		return array_filter($this->getQuestions(), static function (LeadQuestion $question): bool {
			return $question->is_boolean;
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

	/**
	 * @return LeadQuestion[]
	 */
	public function getQuestions(): array {
		if ($this->questions === null) {

			$query = LeadQuestion::find()
				->indexBy('id')
				->andWhere(['is_active' => true])
				->forStatus($this->status_id)
				->forType($this->lead_type_id);
			if ($this->getModel()->isNewRecord && $this->lead !== null) {
				$answeredQuestionsIds = $this->lead
					->getAnswers()
					->select('question_id')
					->column();

				$query->andFilterWhere(['not', ['id' => $answeredQuestionsIds]]);
			}
			$this->questions = $query->all();
			LeadQuestion::sortByOrder($this->questions);
		}
		return $this->questions;
	}

	public function save(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			if ($this->getErrors()) {
				Yii::warning([
					'errors' => $this->getErrors(),
					'attributes' => $this->getAttributes(),
				], __METHOD__ . '.validateModel');
			}
			foreach ($this->getAnswersModels() as $model) {
				if ($model->hasErrors()) {
					Yii::warning($model->getQuestion()->id);
					Yii::warning($model->answer);
					Yii::warning($model->getErrors(), __METHOD__ . '.validateAnswerModel');
				}
			}
			return false;
		}

		$model = $this->getModel();
		$model->details = $this->details;
		$model->old_status_id = $this->lead->getStatusId();
		$model->status_id = $this->status_id;
		$model->owner_id = $this->owner_id;
		$model->lead_id = $this->lead->getId();
		$model->is_pinned = $this->is_pinned;
		$isNewRecord = $model->isNewRecord;
		if (!$model->save()) {
			Yii::warning($model->getErrors(), __METHOD__ . '.save');
			return false;
		}
		$this->linkUser();

		if ($this->leadName !== $this->lead->getName()) {
			$this->lead->updateName($this->leadName);
		}
		if ($this->status_id !== $this->lead->getStatusId()) {
			$this->lead->updateStatus($this->status_id);
		}

		if ($this->withAnswers) {
			$this->linkAnswers(!$isNewRecord);
		}
		if ($isNewRecord) {
			$this->reportSameContacts();
		}
		$this->saveAddress();

		return true;
	}

	protected function reportSameContacts(): bool {
		if (!$this->withSameContacts || empty($this->getSameContacts())) {
			return false;
		}
		$models = $this->getSameContacts();
		foreach ($models as $lead) {
			$this->reportSameContact($lead);
		}
		return true;
	}

	protected function reportSameContact(ActiveLead $lead): void {
		$model = new LeadReport();
		$model->details = Yii::t('lead', 'Report from same contact Lead: #{id}', [
			'id' => $this->getLead()->getId(),
		]);
		$model->old_status_id = $lead->getStatusId();
		$model->status_id = $this->status_id;
		$model->owner_id = $this->owner_id;
		$model->lead_id = $lead->getId();
		$model->save();
		if ($this->status_id !== $lead->getStatusId()) {
			$lead->updateStatus($this->status_id);
		}
		if ($this->leadName !== $lead->getName()) {
			$lead->updateName($this->leadName);
		}
	}

	protected function linkUser(): void {
		if ($this->withLinkUsers) {
			if ($this->linkUserTypeForLeadNotForReportOwner && !$this->lead->isForUser($this->owner_id)) {
				$type = array_key_exists(LeadUser::TYPE_OWNER, $this->lead->getUsers())
					? $this->linkUserTypeForLeadNotForReportOwner
					: LeadUser::TYPE_OWNER;
				$this->lead->linkUser($type, $this->owner_id);
			}
			if (!empty($this->tele_id) && !isset($this->lead->getUsers()[LeadUser::TYPE_TELE])) {
				$this->lead->linkUser(LeadUser::TYPE_TELE, $this->tele_id);
			}
			if (!empty($this->partner_id) && !isset($this->lead->getUsers()[LeadUser::TYPE_PARTNER])) {
				$this->lead->linkUser(LeadUser::TYPE_TELE, $this->partner_id);
			}
		}
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
			&& ($this->withAddress ? $this->getAddress()->validate() : true);
	}

	public function setLead(ActiveLead $lead, bool $withFields = true): void {
		$this->lead = $lead;
		if ($withFields) {
			$this->status_id = $lead->getStatusId();
			$this->lead_type_id = $lead->getSource()->getType()->getID();
			$this->leadName = $lead->getName();
			if (isset($lead->addresses[$this->addressType])) {
				$this->address = $lead->addresses[$this->addressType]->address ?? null;
				$this->withAddress = true;
			}
			$this->tele_id = $lead->getUsers()[LeadUser::TYPE_TELE] ?? null;
			$this->partner_id = $lead->getUsers()[LeadUser::TYPE_PARTNER] ?? null;
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
		$this->is_pinned = $model->is_pinned;
		$this->setAnswers($model->answers);
	}

	public function setAnswers(array $answers): void {
		$this->setClosedAnswers($answers);
	}

	public function getTeleUsersNames(): array {
		return User::getSelectList(
			LeadUser::userIds(LeadUser::TYPE_TELE)
		);
	}

	protected function setClosedAnswers(array $answers): void {
		$closed = $this->getClosedQuestions();
		foreach ($answers as $answer) {
			if (isset($closed[$answer->question_id])) {
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

	public static function getStatusNames(): array {
		return LeadStatus::getNames();
	}

	private function saveAddress(): bool {
		if ($this->withAddress && $this->getAddress()->save()) {
			$address = $this->getLead()->addresses[$this->addressType] ?? new LeadAddress(['type' => $this->addressType]);
			$address->lead_id = $this->getLead()->id;
			$address->address_id = $this->getAddress()->id;
			return $address->save();
		}
		return true;
	}
}
