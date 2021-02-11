<?php

namespace common\modules\lead\models;

use udokmeci\yii2PhoneValidator\PhoneValidator;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class LeadForm extends Model {

	public ?string $status_id = null;
	public ?string $type_id = null;
	public string $date_at = '';
	public string $source = '';
	public string $data = '';
	public string $phone = '';
	public string $email = '';
	public string $postal_code = '';
	public ?int $owner_id = null;

	private ?Lead $model = null;

	public function rules(): array {
		return [
			[['status_id', 'type_id', 'date_at', 'source'], 'required'],
			['phone', PhoneValidator::class, 'country' => 'PL'],
		];
	}

	public static function getStatusNames(): array {
		return ArrayHelper::map(LeadStatus::find()->orderBy('sort_index')->all(), 'id', 'name');
	}

	public static function getTypesNames(): array {
		return ArrayHelper::map(LeadType::find()->orderBy('sort_index')->all(), 'id', 'name');
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}

		$model = $this->getModel();
		$model->type_id = $this->type_id;
		$model->status_id = $this->status_id;
		$model->date_at = $this->date_at;
		$model->source = $this->source;
		$model->data = $this->data;
		$model->phone = $this->phone;
		$model->postal_code = $this->postal_code;
		$model->email = $this->email;
		return $model->save();
	}

	public function getModel(): Lead {
		if ($this->model === null) {
			$this->model = new Lead();
		}
		return $this->model;
	}
}
