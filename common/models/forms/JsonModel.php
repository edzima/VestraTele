<?php

namespace common\models\forms;

use yii\base\Model;

class JsonModel extends Model {

	public string $json = '';

	public function getJsonAttribute(): string {
		return 'json';
	}

	public function rules(): array {
		return [
			['json', 'required',],
			['json', 'string'],
		];
	}
}
