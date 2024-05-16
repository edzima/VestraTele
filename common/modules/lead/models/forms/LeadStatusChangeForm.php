<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadStatus;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class LeadStatusChangeForm extends Model {

	public const SCENARIO_NOT_REQUIRED = 'not-require';
	public array $ids = [];
	public ?int $status_id = null;
	public int $owner_id;

	public function rules(): array {
		return [
			[['ids', 'status_id', '!owner_id'], 'required', 'except' => static::SCENARIO_NOT_REQUIRED],
			['status_id', 'integer'],
			['status_id', 'in', 'range' => array_keys(static::getStatusNames())],
		];
	}

	public function attributeLabels(): array {
		return [
			'status_id' => Yii::t('lead', 'Status'),
		];
	}

	public function save(): ?int {
		if (!$this->validate()) {
			return null;
		}
		$leadsIds = Lead::find()
			->select(['id', 'status_id'])
			->andWhere(['id' => $this->ids])
			->andWhere(['!=', 'status_id', $this->status_id])
			->asArray()
			->all();

		if (empty($leadsIds)) {
			return null;
		}
		$leadsIds = ArrayHelper::map($leadsIds, 'id', 'status_id');
		$rows = [];
		foreach ($leadsIds as $leadId => $statusId) {
			$rows[] = [
				'lead_id' => $leadId,
				'owner_id' => $this->owner_id,
				'old_status_id' => $statusId,
				'status_id' => $this->status_id,
			];
		}
		Lead::updateAll(['status_id' => $this->status_id], [
			'id' => $this->ids,
		]);
		return LeadReport::getDb()->createCommand()
			->batchInsert(
				LeadReport::tableName(),
				[
					'lead_id',
					'owner_id',
					'old_status_id',
					'status_id',
				], $rows
			)->execute();
	}

	public static function getStatusNames(): array {
		return LeadStatus::getNames();
	}

	public function getStatusName(): ?string {
		return static::getStatusNames()[$this->status_id] ?? null;
	}
}
