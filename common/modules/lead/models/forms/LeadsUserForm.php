<?php

namespace common\modules\lead\models\forms;

use common\models\user\User;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadUser;
use common\modules\lead\models\query\LeadQuery;
use common\modules\lead\Module;
use Yii;
use yii\base\Model;

class LeadsUserForm extends Model {

	public const SCENARIO_SINGLE = 'single';
	public const SCENARIO_NOT_REQUIRED = 'not-required';

	public array $leadsIds = [];

	public bool $withOwner = true;
	public bool $sendEmail = true;

	public ?string $userId = null;
	public ?string $type = null;

	public ?int $idUserLeads = null;
	public const SCENARIO_USER_LEADS = 'user-leads';

	public function getTypesNames(): array {
		$types = LeadUser::getTypesNames();
		if (!$this->withOwner) {
			unset($types[LeadUser::TYPE_OWNER]);
		}
		return $types;
	}

	public static function getUsersNames(): array {
		return Module::userNames();
	}

	public function rules(): array {
		return [
			[['userId', 'type', 'leadsIds'], 'required', 'except' => static::SCENARIO_NOT_REQUIRED],
			['!idUserLeads', 'required', 'on' => self::SCENARIO_USER_LEADS],
			['!leadsIds', 'required', 'on' => static::SCENARIO_SINGLE],
			['userId', 'integer'],
			['type', 'string'],
			['sendEmail', 'boolean'],
			[
				'leadsIds',
				'exist',
				'skipOnError' => true,
				'allowArray' => true,
				'targetClass' => Lead::class,
				'targetAttribute' => 'id',
				'enableClientValidation' => false,
				'filter' => function (LeadQuery $query) {
					if ($this->scenario === self::SCENARIO_USER_LEADS) {
						$query->user($this->idUserLeads);
					}
				},
			],
			[
				'userId', 'in', 'range' => array_keys(static::getUsersNames()),
			],
			['type', 'in', 'range' => array_keys($this->getTypesNames())],
		];
	}

	public function load($data, $formName = null) {
		$Load = parent::load($data, $formName);
		return $Load;
	}

	public function attributeLabels(): array {
		return [
			'userId' => Yii::t('lead', 'User'),
			'type' => Yii::t('lead', 'Type'),
			'sendEmail' => Yii::t('lead', 'Send Email'),
		];
	}

	public function save(): ?int {
		if (!$this->validate()) {
			return null;
		}
		if ($this->scenario === static::SCENARIO_SINGLE || count($this->leadsIds) === 1) {
			$leadId = reset($this->leadsIds);
			return $this->saveSingle($leadId, $this->type, $this->userId);
		}
		return $this->saveMultiple();
	}

	protected function saveMultiple(): int {
		if (empty($this->leadsIds)) {
			return 0;
		}
		$rows = [];
		foreach ($this->leadsIds as $leadId) {
			$rows[] = [
				'lead_id' => $leadId,
				'user_id' => $this->userId,
				'type' => $this->type,
			];
		}
		LeadUser::deleteAll(
			[
				'lead_id' => $this->leadsIds,
				'type' => $this->type,
			]
		);
		return LeadUser::getDb()->createCommand()
			->batchInsert(LeadUser::tableName(), ['lead_id', 'user_id', 'type'], $rows)
			->execute();
	}

	public function sendEmail(): ?int {
		if (!$this->sendEmail) {
			return null;
		}
		$email = User::findOne($this->userId)->email ?? null;
		if ($email === null) {
			return null;
		}
		$count = 0;

		foreach ($this->leadsIds as $leadId) {
			$lead = Lead::findById($leadId);
			if ($lead) {
				$pushEmailModel = new LeadPushEmail($lead);
				$pushEmailModel->email = $email;
				$count += $pushEmailModel->sendEmail();
			}
		}
		return $count;
	}

	private function saveSingle(int $leadId, string $type, int $userId) {
		$user = LeadUser::find()
			->andWhere([
				'lead_id' => $leadId,
				'type' => $type,
			])
			->one();
		if ($user === null) {
			$user = new LeadUser();
			$user->lead_id = $leadId;
			$user->type = $type;
		}
		$user->user_id = $userId;
		return $user->save();
	}

	public function getUserName(): ?string {
		return static::getUsersNames()[$this->userId] ?? null;
	}

	public function getTypeName(): ?string {
		return static::getTypesNames()[$this->type] ?? null;
	}

}
