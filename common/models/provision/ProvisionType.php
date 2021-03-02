<?php

namespace common\models\provision;

use DateTime;
use Decimal\Decimal;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * This is the model class for table "provision_type".
 *
 * @property int $id
 * @property string $name
 * @property string|null $from_at
 * @property string|null $to_at
 * @property boolean $only_with_tele
 * @property boolean is_default
 * @property string $data
 * @property string $value
 * @property boolean $is_percentage
 * @property boolean $is_active
 *
 * @property-read ProvisionUser[] $provisionUsers
 */
class ProvisionType extends ActiveRecord {

	public const KEY_DATA_WITH_HIERARCHY = 'with-hierarchy';

	/**
	 * @var static[]|null
	 */
	private static ?array $TYPES = null;
	/**
	 * @var static[]|null
	 */
	private static ?array $ACTIVE_TYPES = null;

	public function __toString() {
		return $this->getNameWithValue();
	}

	public function beforeSave($insert): bool {
		$this->setDataArray($this->getDataArray());
		return parent::beforeSave($insert);
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%provision_type}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'name' => Yii::t('provision', 'Name'),
			'from_at' => Yii::t('provision', 'From at'),
			'to_at' => Yii::t('provision', 'To at'),
			'only_with_tele' => Yii::t('provision', 'Only with telemarketer'),
			'is_default' => Yii::t('provision', 'Is default'),
			'value' => Yii::t('provision', 'Value'),
			'formattedValue' => $this->getFormattedValueLabel(),
			'is_percentage' => Yii::t('provision', 'Is percentage'),
			'is_active' => Yii::t('common', 'Is active'),
			'with_hierarchy' => Yii::t('provision', 'With hierarchy'),
		];
	}

	public function getProvisionUsers(): ProvisionUserQuery {
		return $this->hasMany(ProvisionUser::class, ['type_id' => 'id']);
	}

	public function getNameWithValue(): string {
		return $this->name . ' - ' . $this->getFormattedValue();
	}

	/**  @todo add Decimal Type. */
	public function getFormattedValue($value = null): string {
		if ($value === null) {
			$value = $this->value;
		}
		if ($this->is_percentage) {
			return Yii::$app->formatter->asPercent($value / 100);
		}
		return Yii::$app->formatter->asCurrency($value);
	}

	public function getValue(): Decimal {
		return new Decimal($this->value);
	}

	public function getDataArray(): array {
		return Json::decode($this->data) ?? [];
	}

	public function isForDate($date): bool {
		if (empty($this->from_at) && empty($this->to_at)) {
			return true;
		}
		if (!$date instanceof DateTime) {
			$date = new DateTime($date);
		}
		if (!empty($this->from_at)) {
			$fromAt = new DateTime($this->from_at);
			if (empty($this->to_at)) {
				return $date >= $fromAt;
			}
			return $date >= $fromAt && $date <= new DateTime($this->to_at);
		}
		return $date <= new DateTime($this->to_at);
	}

	public function setWithHierarchy(bool $withHierarchy): void {
		$this->setDataValues(static::KEY_DATA_WITH_HIERARCHY, $withHierarchy);
	}

	public function getWithHierarchy(): bool {
		return $this->getDataArray()[static::KEY_DATA_WITH_HIERARCHY] ?? false;
	}

	protected function setDataValues(string $key, $values): void {
		$data = $this->getDataArray();
		$data[$key] = $values;
		$this->setDataArray($data);
	}

	private function setDataArray(array $data): void {
		$this->data = Json::encode($data);
	}

	protected function getFormattedValueLabel(): string {
		if ($this->is_percentage) {
			return Yii::t('provision', 'Provision (%)');
		}
		return Yii::t('provision', 'Provision ({currencySymbol})', ['currencySymbol' => Yii::$app->formatter->getCurrencySymbol()]);
	}

	public static function getTypesNames(bool $onlyActive = true, bool $refresh = false): array {
		return ArrayHelper::map(static::getTypes($onlyActive, $refresh), 'id', 'nameWithValue');
	}

	/**
	 * @param int $id
	 * @return static|null
	 */
	public static function getType(int $id, bool $onlyActive) {
		if (!isset(static::getTypes()[$id])) {
			static::getTypes()[$id] = static::findOne($id);
		}
		return static::getTypes()[$id];
	}

	/**
	 * @param bool $onlyActive
	 * @param bool $refresh
	 * @return static[]
	 */
	public static function getTypes(bool $onlyActive = false, bool $refresh = false): array {
		if (static::$TYPES === null || $refresh) {
			static::$TYPES = static::find()
				->indexBy('id')
				->all();
		}
		if ($onlyActive) {
			if (empty(static::$ACTIVE_TYPES) || $refresh) {
				static::$ACTIVE_TYPES = [];
				foreach (static::$TYPES as $type) {
					if ($type->is_active) {
						static::$ACTIVE_TYPES[$type->id] = $type;
					}
				}
			}
			return static::$ACTIVE_TYPES;
		}
		return static::$TYPES;
	}

	/**
	 * {@inheritDoc}
	 */
	public static function find(): ProvisionTypeQuery {
		return new ProvisionTypeQuery(static::class);
	}

}
