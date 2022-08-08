<?php

namespace common\models\issue\form;

use common\models\issue\Issue;
use common\models\issue\IssueInterface;
use common\models\issue\IssueUser;
use common\models\issue\query\IssueQuery;
use common\models\query\PhonableQuery;
use common\models\user\User;
use common\modules\lead\models\Lead;
use common\modules\lead\models\query\LeadQuery;
use common\validators\PhoneValidator;
use yii\base\Model;

class IssueLeadPhone extends Model {

	public $phone;
	public $leadType;

	public array $usersTypes = IssueUser::TYPES_CUSTOMERS;

	private IssueInterface $issue;

	public function rules(): array {
		return [
			['leadType', 'integer'],
			['phone', 'each', 'rule' => [PhoneValidator::class]],
		];
	}

	public static function createForUser(User $user): self {
		return new static([
			'phone' => static::getUserPhones($user),
		]);
	}

	public function setIssue(IssueInterface $issue): void {
		$this->issue = $issue;
		if (!$issue->getIssueModel()->isNewRecord) {
			$this->phone = $this->getIssueUsersPhones();
		}

		$this->leadType = $this->issue->getIssueType()->lead_type_id;
	}

	/**
	 * @param IssueUser[]|null $users , when null get Users from Issue Model
	 * @param string[]|null $types , when null get from Self $userTypes attribute
	 * @return string[]
	 */
	public function getIssueUsersPhones(?array $users = null, ?array $types = null): array {
		if ($users === null) {
			$users = $this->issue->getIssueModel()->users;
		}
		if (empty($users)) {
			return [];
		}
		if ($types === null) {
			$types = $this->usersTypes;
		}
		$phones = [];
		foreach ($users as $user) {
			if (!empty($types) && !in_array($user->type, $types, true)) {
				continue;
			}
			$userPhones = static::getUserPhones($user->user);
			if (!empty($userPhones)) {
				$phones[] = $userPhones;
			}
		}
		return array_merge(...$phones);
	}

	public function findLeads(): LeadQuery {
		$query = Lead::find()
			->withPhoneNumber($this->phone);
		if (!empty($this->leadType)) {
			$query->type($this->leadType);
		}
		return $query;
	}

	public function findIssues(): IssueQuery {
		return Issue::find()
			->joinWith([
				'users.user.userProfile' => function (PhonableQuery $query): void {
					$query->withPhoneNumber($this->phone);
				},
			])
			->distinct();
	}

	public static function getUserPhones(User $user): array {
		$phones = [];
		if (!empty($user->profile->phone)) {
			$phones[] = $user->profile->phone;
		}
		if (!empty($user->profile->phone_2)) {
			$phones[] = $user->profile->phone_2;
		}
		return $phones;
	}

}
