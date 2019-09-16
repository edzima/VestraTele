<?php

namespace common\models\benefit;

use common\models\benefit\query\BenefitAmountQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "benefit_amount".
 *
 * @property int $id
 * @property int $type
 * @property string $from_at
 * @property string $to_at
 * @property string $value
 *
 * @property-read string $typeName
 */
class BenefitAmount extends ActiveRecord {

	public const TYPE_SMALLER = 1;
	public const TYPE_GREATER = 2;

	public static $_instances;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return 'benefit_amount';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['type', 'from_at', 'to_at', 'value'], 'required'],
			[['type'], 'integer'],
			[['from_at', 'to_at'], 'date', 'format' => DATE_ATOM],
			['from_at', 'compare', 'compareAttribute' => 'to_at', 'operator' => '<', 'enableClientValidation' => false],
			[['value'], 'number', 'min' => 1],
			['type', 'in', 'range' => array_keys(static::getTypesNames())],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'type' => 'Rodzaj',
			'from_at' => 'Od',
			'to_at' => 'Do',
			'value' => 'Wysokość',
			'typeName' => 'Rodzaj',
		];
	}

	public function isSmaller(): bool {
		return $this->type === static::TYPE_SMALLER;
	}

	public function isGreater(): bool {
		return $this->type === static::TYPE_GREATER;
	}

	public function getTypeName(): string {
		return static::getTypesNames()[$this->type];
	}

	public static function getTypesNames(): array {
		return [
			static::TYPE_SMALLER => 'Zasiłek dla opiekuna',
			static::TYPE_GREATER => 'Świadczenie pielęgnacyjne',
		];
	}

	/**
	 * {@inheritdoc}
	 * @return BenefitAmountQuery the active query used by this AR class.
	 */
	public static function find() {
		return new BenefitAmountQuery(static::class);
	}

	/**
	 * @return static[]
	 */
	public static function instances(): array {
		if (empty(static::$_instances)) {
			static::$_instances = static::find()->all();
		}
		return static::$_instances;
	}
}
