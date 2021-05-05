<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\LeadReportSchema;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadType;
use yii\base\Model;

class LeadReportSchemaForm extends Model {

	public string $name = '';
	public ?string $placeholder = null;

	public ?bool $show_in_grid = null;
	public ?bool $is_required = null;

	public $type_id;
	public $status_id;

	private ?LeadReportSchema $model = null;

	public function rules(): array {
		return [
			[['name'], 'required'],
			[['show_in_grid', 'is_required'], 'boolean'],
			['type_id', 'in', 'range' => array_keys(static::getStatusNames())],
			['status_id', 'in', 'range' => array_keys(static::getStatusNames())],
		];
	}

	public function attributeLabels(): array {
		return LeadReportSchema::instance()->attributeLabels();
	}

	public function setModel(LeadReportSchema $model): void {
		$this->model = $model;
		$this->name = $model->name;
		$this->placeholder = $model->placeholder;
		$this->show_in_grid = $model->show_in_grid;
		$this->is_required = $model->is_required;
		$this->type_id = $model->type_id;
		$this->status_id = $model->status_id;
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
		$model->show_in_grid = $this->show_in_grid;
		$model->type_id = $this->type_id;
		$model->status_id = $this->status_id;
		return $model->save();
	}

}
