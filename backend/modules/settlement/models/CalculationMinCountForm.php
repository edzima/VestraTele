<?php

namespace backend\modules\settlement\models;

use common\models\issue\IssueType;
use common\models\issue\StageType;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class CalculationMinCountForm extends Model {

	public ?int $typeId = null;
	public ?int $stageId = null;
	public int $minCount = 1;

	public function rules(): array {
		return [
			[['stageId', 'typeId', 'minCount'], 'required'],
			[['stageId', 'typeId', 'minCount'], 'integer'],
			['minCount', 'integer', 'min' => 0],
			['typeId', 'in', 'range' => array_keys(static::getTypesNames())],
			[['stageId'], 'filter', 'filter' => 'intval'],
			[
				'stageId', 'in', 'when' => function (): bool {
				return !empty($this->typeId);
			}, 'range' => function () {
				return array_keys($this->getStagesNames());
			}, 'enableClientValidation' => false,
			],
		];
	}

	public function attributeLabels(): array {
		return [
			'stageId' => Yii::t('backend', 'Stage'),
			'typeId' => Yii::t('backend', 'Type'),
			'minCount' => Yii::t('backend', 'Min count'),
		];
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}

		Yii::$app->db->createCommand()
			->update(StageType::tableName(),
				[
					'min_calculation_count' => empty($this->minCount) ? null : $this->minCount,
				],
				['stage_id' => $this->stageId, 'type_id' => $this->typeId],
			)
			->execute();
		return true;
	}

	public static function getTypesNames(): array {
		return IssueType::getTypesNames();
	}

	public function getStagesNames(): array {
		if (empty($this->typeId)) {
			return [];
		}
		return static::getStages($this->typeId);
	}

	private static function getStages(int $typeID): array {
		$type = IssueType::get($typeID);
		if ($type === null) {
			return [];
		}
		return ArrayHelper::map($type->stages, 'id', 'name');
	}

}
