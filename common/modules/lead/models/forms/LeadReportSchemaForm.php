<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\LeadReportSchema;
use common\modules\lead\models\LeadReportSchemaStatusType;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadType;
use yii\base\Model;

class LeadReportSchemaForm extends Model {

	public string $name = '';
	public ?string $placeholder = null;

	public array $types_ids = [];
	public array $status_ids = [];

	private ?LeadReportSchema $model = null;

	public function rules(): array {
		return [
			[['name', 'status_ids', 'types_ids'], 'required'],
		];
	}

	public function setModel(LeadReportSchema $model): void {
		$this->model = $model;
		$this->name = $model->name;
		$this->placeholder = $model->placeholder;
		$this->types_ids = $model->getTypesIds();
		$this->status_ids = $model->getStatusIds();
	}

	public function getModel(): LeadReportSchema {
		if ($this->model === null) {
			$this->model = new LeadReportSchema();
		}
		return $this->model;
	}

	public static function getStatusNames(): array {
		return LeadStatus::getNames();
	}

	public static function getTypesNames(): array {
		return LeadType::getNames();
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		$model = $this->getModel();
		$model->name = $this->name;
		$model->placeholder = $this->placeholder;
		$isNewRecord = $model->getIsNewRecord();
		if (!$model->save()) {
			return false;
		}
		if (!$isNewRecord) {
			$model->unlinkAll('schemaStatusTypes', true);
		}
		$rows = [];
		foreach ($this->status_ids as $status_id) {
			foreach ($this->types_ids as $type_id) {
				$rows[] = [
					'schema_id' => $model->id,
					'status_id' => $status_id,
					'type_id' => $type_id,
				];
			}
		}
		return LeadReportSchemaStatusType::getDb()->createCommand()->batchInsert(
			LeadReportSchemaStatusType::tableName(),
			['schema_id', 'status_id', 'type_id'],
			$rows)->execute();
	}

}
