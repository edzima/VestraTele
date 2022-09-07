<?php

namespace common\modules\reminder\models;

use yii\base\Model;

class ReminderForm extends Model {

	public string $dateFormat = 'YYYY-MM-DD HH:mm';

	public ?int $priority = null;
	public ?string $details = null;
	public ?string $date_at = null;

	private ?Reminder $model = null;

	public function rules(): array {
		return [
			[['priority', 'date_at'], 'required'],
			['details', 'string'],
			[['date_at'], 'date', 'format' => $this->dateFormat],
		];
	}

	public function attributeLabels(): array {
		return Reminder::instance()->attributeLabels();
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		$model = $this->getModel();
		$model->priority = $this->priority;
		$model->details = $this->details;
		$model->date_at = $this->date_at;
		return $model->save();
	}

	public function getModel(): Reminder {
		if ($this->model === null) {
			$this->model = new Reminder();
		}
		return $this->model;
	}

	public static function getPriorityNames(): array {
		return Reminder::getPriorityNames();
	}

	public function setModel(Reminder $model): void {
		$this->model = $model;
		$this->details = $model->details;
		$this->date_at = $model->date_at;
		$this->priority = $model->priority;
	}
}
