<?php

namespace common\modules\calendar\models;

use Yii;
use yii\base\Model;
use yii\helpers\Json;

class Filter extends Model {

	public string $color = '';

	public function rules(): array {
		return [
			['color', 'required'],
		];
	}

	public function attributeLabels(): array {
		return [
			'color' => Yii::t('calendar', 'Color'),
		];
	}

	public function toJson(): string {
		$data = [];
		if ($this->color) {
			$data['color'] = $this->color;
		}
		return Json::encode($data);
	}

}
