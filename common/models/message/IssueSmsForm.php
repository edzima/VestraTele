<?php

namespace common\models\message;

use common\models\issue\Issue;
use common\models\issue\IssueInterface;
use common\models\issue\IssueNote;
use common\models\issue\IssueNoteForm;
use common\models\issue\IssueUser;
use common\models\user\User;
use console\jobs\IssueSmsSendJob;
use Edzima\Yii2Adescom\models\MessageInterface;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;

class IssueSmsForm extends SmsForm {

	public array $userTypes = [];

	public int $owner_id;
	public bool $allowSelf = false;
	public ?string $note_title = null;

	public string $noteTitleTemplate = '{title} - {userTypeWithPhone}';
	public string $userTypeWithPhone = '{type}: {userWithPhone}';
	public string $userWithPhoneTemplate = '{user}[{phone}]';

	private IssueInterface $_issue;

	/**
	 * @param IssueInterface $issue
	 * @param array $config
	 */
	public function __construct(IssueInterface $issue, array $config = []) {
		$this->_issue = $issue;
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
	}

	public function rules(): array {
		return array_merge(
			parent::rules(), [
			[['!owner_id'], 'required'],
			['phone', 'in', 'range' => $this->getPhones(), 'on' => static::SCENARIO_SINGLE],
			['phones', 'in', 'range' => $this->getPhones(), 'allowArray' => true, 'on' => static::SCENARIO_MULTIPLE],
		],
		);
	}

	public function attributeLabels(): array {
		return array_merge(parent::attributeLabels(), [
			'note_title' => Yii::t('issue', 'Note Title'),
		]);
	}

	public function setFirstAvailablePhone(): void {
		$phones = $this->getPhonesData();
		if (empty($phones)) {
			$this->phones = [];
			$this->phone = '';
			return;
		}
		if ($this->isMultiple()) {
			$this->phones = [];
			foreach ($phones as $userPhones) {
				$phone = array_key_first($userPhones);
				if (!empty($phone)) {
					$this->phones[] = $phone;
				}
			}
		} else {
			$this->phone = array_key_first($phones[array_key_first($phones)]);
		}
	}

	public function note(string $smsId, bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		$issueNote = new IssueNoteForm();
		$issueNote->user_id = $this->owner_id;
		$issueNote->issue_id = $this->getIssue()->getIssueId();
		$issueNote->type = IssueNote::genereateSmsType($this->phone, $smsId);
		$issueNote->title = strtr($this->noteTitleTemplate, [
			'{title}' => $this->note_title,
			'{userTypeWithPhone}' => $this->getPhoneIssueUserName(),
		]);
		$issueNote->description = $this->message;
		if ($issueNote->save()) {
			return true;
		}

		Yii::error($issueNote->getErrors(), __METHOD__);
		return false;
	}

	public function getPhoneIssueUserName(string $phone = null, string $userType = null): ?string {
		if ($phone === null) {
			$phone = $this->phone;
		}
		$phone = static::normalizePhone($phone);
		if (!empty($phone)) {
			foreach ($this->getPhonesData() as $userTypeName => $phonesDatum) {
				if (isset($phonesDatum[$phone])
					&& ($userType === null || IssueUser::getTypesNames()[$userType] === $userTypeName)) {
					return strtr($this->userTypeWithPhone, [
						'{type}' => $userTypeName,
						'{userWithPhone}' => $phonesDatum[$phone],
					]);
				}
			}
		}
		return null;
	}

	public function getIssue(): IssueInterface {
		return $this->_issue;
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
			if ($this->allowUserTypePhones($issueUser)) {
				$phonesTypes = [];
				$phone1 = $issueUser->user->profile->phone;
				$phone2 = $issueUser->user->profile->phone_2;

				if (!empty($phone1) && ($normalize1 = static::normalizePhone($phone1)) !== null) {
					$phonesTypes[$normalize1] = $this->userNameWithPhone($issueUser->user, $phone1);
				}
				if (!empty($phone2) && ($normalize2 = static::normalizePhone($phone2)) !== null) {
					$phonesTypes[$normalize2] = $this->userNameWithPhone($issueUser->user, $phone2);
				}

				if (!empty($phonesTypes)) {
					$phones[$issueUser->getTypeName()] = $phonesTypes;
				}
			}
		}
		return $phones;
	}

	private function allowUserTypePhones(IssueUser $user): bool {
		return in_array($user->type, $this->userTypes, true)
			&& ($user->user_id !== $this->owner_id || $this->allowSelf);
	}

	protected function userNameWithPhone(User $user, string $phone): string {
		return strtr($this->userWithPhoneTemplate, [
			'{user}' => $user->getFullName(),
			'{phone}' => $phone,
		]);
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

	/**
	 * @throws InvalidArgumentException
	 */
	public static function createFromJob(IssueSmsSendJob $job): self {
		return static::createFromIssueId($job->issue_id, [
			'owner_id' => $job->owner_id,
			'message' => $job->message->getMessage(),
			'phone' => $job->message->getDst(),
			'note_title' => $job->note_title,
		]);
	}

	/**
	 * @param int $issue_id
	 * @param array $config
	 * @return static
	 * @throw InvalidArgumentException
	 */
	public static function createFromIssueId(int $issue_id, array $config = []) {
		$issue = static::findIssue($issue_id);
		if (!$issue) {
			throw new InvalidArgumentException('Not Found Valid Issue for $id: ' . $issue_id);
		}
		return new static($issue, $config);
	}

	public static function findIssue(int $issue_id): ?IssueInterface {
		return Issue::find()
			->andWhere(['id' => $issue_id])
			->withoutArchives()
			->one();
	}

}
