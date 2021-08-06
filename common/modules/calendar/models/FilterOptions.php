<?php

namespace common\modules\calendar\models;

use Yii;
use yii\base\Model;

class FilterOptions extends Model {

	public string $color = '';

	public function rules(): array {
		return [
			['color', 'string'],
		];
	}

	public function attributeLabels(): array {
		return [
			'color' => Yii::t('calendar', 'Color'),
		];
	}

}
