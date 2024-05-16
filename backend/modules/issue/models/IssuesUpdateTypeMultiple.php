<?php

namespace backend\modules\issue\models;

use common\models\issue\Issue;
use common\models\issue\IssueType;
use Yii;
use yii\base\Model;

class IssuesUpdateTypeMultiple extends Model {

	public $ids;

	public $typeId;

	public static function getTypesNames(): array {
		return IssueType::getTypesNames();
	}

	public function rules(): array {
		return [
			[['ids', 'typeId'], 'required'],
			['typeId', 'integer'],
			['typeId', 'in', 'range' => array_keys(static::getTypesNames())],
		];
	}

	public function attributeLabels(): array {
		return [
			'typeId' => Yii::t('issue', 'Type'),
		];
	}

	public function update(bool $validate = true): ?int {
		if ($validate && $this->validate()) {
			return null;
		}

		return Issue::updateAll(['type_id' => $this->typeId], [
			'id' => $this->ids,
		]);
	}
}
