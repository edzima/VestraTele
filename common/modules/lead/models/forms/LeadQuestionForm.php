<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\LeadQuestion;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadType;
use yii\base\Model;

class LeadQuestionForm extends Model {

	public string $name = '';
	public ?string $placeholder = null;

	public ?bool $is_active = true;
	public ?bool $is_boolean = false;
	public ?bool $show_in_grid = null;
	public ?bool $is_required = null;

	public ?int $order = null;
	public $type_id;
	public $status_id;

	private ?LeadQuestion $model = null;

	public function rules(): array {
		return [
			[['name'], 'required'],
			[['order'], 'integer'],
			[['show_in_grid', 'is_required', 'is_active', 'is_boolean'], 'boolean'],
			[['name', 'placeholder'], 'string'],
			['placeholder', 'default', 'value' => null],
			['type_id', 'in', 'range' => array_keys(static::getTypesNames())],
			['status_id', 'in', 'range' => array_keys(static::getStatusNames())],
		];
	}

	public function attributeLabels(): array {
		return LeadQuestion::instance()->attributeLabels();
	}

	public function setModel(LeadQuestion $model): void {
		$this->model = $model;
		$this->name = $model->name;
		$this->placeholder = $model->placeholder;
		$this->show_in_grid = $model->show_in_grid;
		$this->is_required = $model->is_required;
		$this->is_active = $model->is_active;
		$this->is_boolean = $model->is_boolean;
		$this->type_id = $model->type_id;
		$this->status_id = $model->status_id;
		$this->order = $model->order;
	}

	public function getModel(): LeadQuestion {
		if ($this->model === null) {
			$this->model = new LeadQuestion();
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
		$model->is_required = $this->is_required;
		$model->is_active = $this->is_active;
		$model->is_boolean = $this->is_boolean;
		$model->show_in_grid = $this->show_in_grid;
		$model->type_id = $this->type_id;
		$model->status_id = $this->status_id;
		$model->order = $this->order;
		return $model->save();
	}

}
