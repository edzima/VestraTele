<?php

namespace common\models\provision;

use Closure;
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
 * @property boolean is_default
 * @property string $data
 * @property string $value
 * @property boolean $is_percentage
 * @property boolean $is_active
 *
 * @property-read ProvisionUser[] $provisionUsers
 */
class ProvisionType extends ActiveRecord {

	protected const INDEX_KEY = 'id';

	public const KEY_DATA_WITH_HIERARCHY = 'with-hierarchy';
	public const KEY_DATA_BASE_TYPE_ID = 'base-type_id';

	/**
	 * @var static[]|null
	 */
	private static ?array $TYPES = null;

	public function __toString() {
		return $this->getNameWithTypeName();
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
			'nameWithTypeName' => Yii::t('provision', 'Name'),
			'from_at' => Yii::t('provision', 'From at'),
			'to_at' => Yii::t('provision', 'To at'),
			'is_default' => Yii::t('provision', 'Is default'),
			'value' => Yii::t('provision', 'Value'),
			'formattedValue' => $this->getFormattedValueLabel(),
			'is_percentage' => Yii::t('provision', 'Is percentage'),
			'is_active' => Yii::t('common', 'Is active'),
			'withHierarchy' => Yii::t('provision', 'With hierarchy'),
			'baseTypeId' => Yii::t('provision', 'Base Type'),
		];
	}

	public function getBaseType(): ?self {
		$typeId = $this->getBaseTypeId();
		if ($typeId === null) {
			return null;
		}
		return static::getType($typeId, false);
	}

	/** @noinspection PhpIncompatibleReturnTypeInspection */
	public function getProvisionUsers(): ProvisionUserQuery {
		return $this->hasMany(ProvisionUser::class, ['type_id' => 'id']);
	}

	public function getNameWithTypeName(): string {
		return $this->name . ' - ( ' . $this->getTypeName() . ' )';
	}

	public function getTypeName(): string {
		if ($this->is_percentage) {
			return '%';
		}
		return Yii::$app->formatter->getCurrencySymbol();
	}

	public function getNameWithValue(): string {
		return $this->name . ' - ' . $this->getFormattedValue();
	}

	/**  @todo add Decimal Type. */
	public function getFormattedValue(?Decimal $value = null): string {
		if ($value === null) {
			$value = $this->getValue();
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

	public function getBaseTypeId(): ?int {
		return $this->getDataArray()[static::KEY_DATA_BASE_TYPE_ID] ?? null;
	}

	public function setBaseTypeId(?int $value): void {
		$this->setDataValues(static::KEY_DATA_BASE_TYPE_ID, $value);
	}

	public function setWithHierarchy(bool $withHierarchy): void {
		$this->setDataValues(static::KEY_DATA_WITH_HIERARCHY, $withHierarchy);
	}

	public function getWithHierarchy(): bool {
		return $this->getDataArray()[static::KEY_DATA_WITH_HIERARCHY] ?? false;
	}

	protected function setDataValues(string $key, $values): void {
		$data = $this->getDataArray();
		if (empty($values)) {
			if (isset($data[$key])) {
				unset($data[$key]);
			}
		} else {
			$data[$key] = $values;
		}
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
		return ArrayHelper::map(static::getTypes($onlyActive, $refresh), 'id', 'nameWithTypeName');
	}

	/**
	 * @param int $id
	 * @param bool $onlyActive
	 * @return static|null
	 */
	public static function getType(int $id, bool $onlyActive): ?self {
		if (!isset(static::getTypes($onlyActive)[$id])) {
			static::$TYPES[$id] = static::findOne($id);
		}
		$type = static::getTypes()[$id];
		if ($onlyActive && !$type->is_active) {
			return null;
		}
		return $type;
	}

	/**
	 * @param bool $onlyActive
	 * @param bool $refresh
	 * @return static[]
	 */
	public static function getTypes(bool $onlyActive = false, bool $refresh = false): array {
		if (static::$TYPES === null || $refresh) {
			static::$TYPES = static::find()
				->indexBy(static::INDEX_KEY)
				->all();
		}
		if ($onlyActive) {
			return static::activeFilter(static::$TYPES);
		}
		return static::$TYPES;
	}

	/**
	 * @param static[] $types
	 * @return static[]
	 */
	public static function activeFilter(array $types): array {
		return static::filter($types, static function (self $model): bool {
			return $model->is_active;
		});
	}

	public static function filter(array $types, Closure $callback): array {
		return ArrayHelper::index(
			array_filter($types, $callback),
			static::INDEX_KEY
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public static function find(): ProvisionTypeQuery {
		return new ProvisionTypeQuery(static::class);
	}

}
