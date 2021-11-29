<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadUser;
use common\modules\lead\Module;
use Yii;
use yii\base\Model;

class LeadsUserForm extends Model {

	public const SCENARIO_SINGLE = 'single';

	public array $leadsIds = [];

	public bool $withOwner = true;

	public ?string $userId = null;
	public ?string $type = null;

	public static function getLeadsIds(): array {
		$ids = Lead::find()->select('id')->column();
		return array_combine($ids, $ids);
	}

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
			[['userId', 'type', 'leadsIds'], 'required'],
			['!leadsIds', 'required', 'on' => static::SCENARIO_SINGLE],
			['userId', 'integer'],
			['type', 'string'],
			['leadsIds', 'exist', 'skipOnError' => true, 'allowArray' => true, 'targetClass' => Lead::class, 'targetAttribute' => 'id', 'enableClientValidation' => false],
			[
				'userId', 'in', 'range' => array_keys(static::getUsersNames()),
			],
			['type', 'in', 'range' => array_keys($this->getTypesNames())],
		];
	}

	public function attributeLabels(): array {
		return [
			'userId' => Yii::t('lead', 'User'),
			'type' => Yii::t('lead', 'Type'),
		];
	}

	public function save(): ?int {
		if (!$this->validate()) {
			return null;
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
			->
			execute();
	}

}
