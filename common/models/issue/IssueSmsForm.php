<?php

namespace common\models\issue;

use common\models\SmsForm;
use console\jobs\IssueSmsSendJob;
use Edzima\Yii2Adescom\models\MessageInterface;
use Yii;
use yii\base\InvalidConfigException;

class IssueSmsForm extends SmsForm {

	public array $userTypes = [];

	public int $owner_id;
	public ?string $note_title = null;

	public bool $withoutArchives = true;

	private IssueInterface $_issue;

	/**
	 * @param int $issue_id
	 * @param array $config
	 * @throws InvalidConfigException
	 */
	public function __construct(int $issue_id, array $config = []) {
		$this->ensureIssue($issue_id);
		parent::__construct($config);
	}

	/**
	 * @throws InvalidConfigException
	 */
	public function init(): void {
		parent::init();
		if ($this->note_title === null) {
			$this->note_title = Yii::t('issue', 'SMS Sent');
		}
		if (empty($this->userTypes)) {
			$this->userTypes = array_keys(IssueUser::getTypesNames());
		}

		if (empty($this->phone) || empty($this->phones)) {
			$phones = $this->getPhones();
			if (count($phones) === 1) {
				$phone = reset($phones);
				if (empty($this->phone)) {
					$this->phone = $phone;
				}
				if (empty($this->phones)) {
					$this->phones = [$phone];
				}
			}
		}
	}

	public function attributeLabels(): array {
		return array_merge(parent::attributeLabels(), [
			'note_title' => Yii::t('issue', 'Note Title'),
		]);
	}

	public function rules(): array {
		return array_merge(
			[
				[['!owner_id'], 'required'],
				['phone', 'in', 'range' => $this->getPhones(), 'on' => static::SCENARIO_SINGLE],
				['phones', 'in', 'range' => $this->getPhones(), 'allowArray' => true, 'on' => static::SCENARIO_MULTIPLE],
			],
			parent::rules()
		);
	}

	/**
	 * @throws InvalidConfigException
	 */
	public static function createFromJob(IssueSmsSendJob $job): self {
		return new static($job->issue_id, [
			'owner_id' => $job->owner_id,
			'message' => $job->message->getMessage(),
			'phone' => $job->message->getDst(),
			'note_title' => $job->note_title,
		]);
	}

	public function note(string $smsId): bool {
		$issueNote = new IssueNoteForm();
		$issueNote->user_id = $this->owner_id;
		$issueNote->issue_id = $this->getIssue()->getIssueId();
		$issueNote->type = IssueNote::generateType(IssueNote::TYPE_SMS, $smsId);
		$issueNote->title = $this->note_title;
		$issueNote->description = $this->message;
		if ($issueNote->save()) {
			return true;
		}
		Yii::error($issueNote->getErrors(), __METHOD__);
		return false;
	}

	public function getIssue(): IssueInterface {
		return $this->_issue;
	}

	protected function createJob(MessageInterface $message = null): IssueSmsSendJob {
		if ($message === null) {
			$message = $this->getMessage();
		}
		return new IssueSmsSendJob([
			'issue_id' => $this->getIssue()->getIssueId(),
			'message' => $message,
			'owner_id' => $this->owner_id,
			'note_title' => $this->note_title,
		]);
	}

	public function getPhones(): array {
		$phones = [];
		foreach ($this->getPhonesData() as $userPhones) {
			foreach ($userPhones as $phone => $text) {
				$phones[$phone] = $phone;
			}
		}
		return $phones;
	}

	public function getPhonesData(): array {
		$phones = [];
		foreach ($this->getIssue()->getIssueModel()->users as $issueUser) {

			if (in_array($issueUser->type, $this->userTypes, true)) {
				$phonesTypes = [];
				$phone1 = $issueUser->user->profile->phone;
				$phone2 = $issueUser->user->profile->phone_2;
				if (!empty($phone1)) {
					$phonesTypes[$phone1] = $issueUser->user->getFullName() . ' - ' . $phone1;
				}
				if (!empty($phone2)) {
					$phonesTypes[$phone2] = $issueUser->user->getFullName() . ' - ' . $phone2;
				}

				if (!empty($phonesTypes)) {
					$phones[$issueUser->getTypeName()] = $phonesTypes;
				}
			}
		}
		return $phones;
	}

	/**
	 * @param int $issue_id
	 * @throws InvalidConfigException
	 */
	private function ensureIssue(int $issue_id): void {
		$issue = $this->findIssue($issue_id);
		if ($issue === null) {
			throw new InvalidConfigException('Not Found Valid Issue with ID: ' . $issue_id);
		}
		$this->_issue = $issue;
	}

	protected function findIssue(int $issue_id): ?IssueInterface {
		$query = Issue::find()
			->andWhere(['id' => $issue_id]);
		if ($this->withoutArchives) {
			$query->withoutArchives();
		}
		return $query->one();
	}

}
