<?php

namespace common\models\issue;

use common\models\SummonTypeOptions;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * This is the model class for table "summon_type".
 *
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property string|null $calendar_background
 * @property string|null $options
 *
 * @property Summon[] $summons
 */
class SummonType extends ActiveRecord {

	private ?SummonTypeOptions $typeModel = null;

	/**
	 * @var mixed|null
	 */
	private static array $MODELS;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%summon_type}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['name', 'short_name'], 'required'],
			[['name'], 'string', 'max' => 100],
			[['short_name'], 'string', 'max' => 10],
			[['calendar_background'], 'string', 'max' => 255],
			['calendar_background', 'default', 'value' => null],
			[['name'], 'unique'],
			[['short_name'], 'unique'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'name' => Yii::t('common', 'Name'),
			'short_name' => Yii::t('common', 'Short Name'),
			'calendar_background' => Yii::t('common', 'Calendar Background'),
		];
	}

	/**
	 * Gets query for [[Summons]].
	 *
	 * @return ActiveQuery
	 */
	public function getSummons() {
		return $this->hasMany(Summon::class, ['type_id' => 'id']);
	}

	public function getNameWithShort(): string {
		return $this->short_name . ' - ' . $this->name;
	}

	public static function getNames(): array {
		return ArrayHelper::map(static::getModels(), 'id', 'name');
	}

	public static function getShortTypesNames(): array {
		return ArrayHelper::map(static::getModels(), 'id', 'short_name');
	}

	public static function getNamesWithShort(): array {
		return ArrayHelper::map(static::getModels(), 'id', 'nameWithShort');
	}

	/**
	 * @return static[]
	 */
	public static function getModels(): array {
		if (empty(static::$MODELS)) {
			static::$MODELS = static::find()->indexBy('id')->all();
		}
		return static::$MODELS;
	}

	public function setOptions(SummonTypeOptions $options): void {
		$this->typeModel = $options;
		$this->options = $options->toJson();
	}

	public function getOptions(): SummonTypeOptions {
		if ($this->typeModel === null) {
			$options = $this->options;
			if (!is_array($options)) {
				$options = Json::decode($options);
			}
			$this->typeModel = new SummonTypeOptions($options);
		}
		return $this->typeModel;
	}

	public function isForFormAttribute(string $attribute): bool {
		$fields = $this->getOptions()->formAttributes;
		if (empty($fields)) {
			return true;
		}
		return in_array($attribute, (array) $fields, true);
	}

	public function hasSummonVisibleField(string $attribute): bool {
		$visibleFields = $this->getOptions()->visibleSummonFields;
		if (empty($visibleFields)) {
			return true;
		}
		return in_array($attribute, (array) $visibleFields, true);
	}

}
