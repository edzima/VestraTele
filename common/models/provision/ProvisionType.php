<?php

namespace common\models\provision;

use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueType;
use common\models\issue\IssueUser;
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

	private const KEY_DATA_ROLES = 'roles';
	private const KEY_DATA_TYPES = 'types';
	public const KEY_DATA_CALCULATION_TYPES = 'calculation.types';
	public const KEY_DATA_ISSUE_USER_TYPE = 'issue.user.type';
	public const KEY_DATA_WITH_HIERARCHY = 'with-hierarchy';
	/**
	 * @var static[]|null
	 */
	private static ?array $TYPES = null;

	public function __toString() {
		return $this->name . ' ' . $this->getFormattedValue();
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
			'value' => Yii::t('common', 'Provision value'),
			'formattedValue' => Yii::t('provision', 'Value'),
			'calculationTypesNames' => Yii::t('settlement', 'Settlement type'),
			'issueTypesNames' => Yii::t('common', 'Issue Types'),
			'is_percentage' => Yii::t('provision', 'Is percentage'),
			'is_active' => Yii::t('common', 'Is active'),
			'rolesNames' => Yii::t('common', 'Roles'),
			'typesNames' => Yii::t('common', 'Issue types'),
			'with_hierarchy' => Yii::t('provision', 'With hierarchy'),
			'issueUserTypeName' => Yii::t('common', 'Issue user type'),
		];
	}

	public function getProvisionUsers(): ProvisionUserQuery {
		return $this->hasMany(ProvisionUser::class, ['type_id' => 'id']);
	}

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

	public function setRoles(array $roles): void {
		$this->setDataValues(static::KEY_DATA_ROLES, $roles);
	}

	public function getRoles(): array {
		return $this->getDataArray()[static::KEY_DATA_ROLES] ?? [];
	}

	public function getCalculationTypes(): array {
		return $this->getDataArray()[static::KEY_DATA_CALCULATION_TYPES] ?? [];
	}

	public function setCalculationTypes(array $types): void {
		$types = array_map('intval', $types);
		$this->setDataValues(static::KEY_DATA_CALCULATION_TYPES, $types);
	}

	public function getIssueTypesIds(): array {
		return $this->getDataArray()[static::KEY_DATA_TYPES] ?? [];
	}

	public function setIssueTypesIds(array $ids): void {
		$ids = array_map('intval', $ids);
		$this->setDataValues(static::KEY_DATA_TYPES, $ids);
	}

	public function getIssueUserType(): string {
		return $this->getDataArray()[static::KEY_DATA_ISSUE_USER_TYPE];
	}

	public function setIssueUserTypes(string $type): void {
		$this->setDataValues(static::KEY_DATA_ISSUE_USER_TYPE, $type);
	}

	public function setWithHierarchy(bool $withHierarchy): void {
		$this->setDataValues(static::KEY_DATA_WITH_HIERARCHY, $withHierarchy);
	}

	public function getWithHierarchy(): bool {
		return $this->getDataArray()[static::KEY_DATA_WITH_HIERARCHY] ?? false;
	}

	private function setDataValues(string $key, $values): void {
		$data = $this->getDataArray();
		$data[$key] = $values;
		$this->setDataArray($data);
	}

	private function getDataArray(): array {
		return Json::decode($this->data) ?? [];
	}

	private function setDataArray(array $data): void {
		$this->data = Json::encode($data);
	}

	public function getCalculationTypesNames(): string {
		$calculationTypes = $this->getCalculationTypes();
		if (empty($calculationTypes)) {
			return Yii::t('common', 'All');
		}
		$allNames = IssuePayCalculation::getTypesNames();
		$names = [];
		foreach ($calculationTypes as $type) {
			$names[] = $allNames[$type];
		}
		return implode(', ', $names);
	}

	public function getIssueTypesNames(): string {
		$types = $this->getIssueTypesIds();
		if (empty($types)) {
			return Yii::t('common', 'All');
		}
		$typesNames = IssueType::getTypesNames();
		$names = [];
		foreach ($types as $id) {
			$names[] = $typesNames[$id];
		}
		return implode(', ', $names);
	}

	public function getIssueUserTypeName(): string {
		return IssueUser::getTypesNames()[$this->getIssueUserType()];
	}

	public function isForCalculationType(int $type): bool {
		$types = $this->getCalculationTypes();
		if (empty($types)) {
			return true;
		}
		return in_array($type, $types, true);
	}

	public function isForIssueType(int $typeID): bool {
		$ids = $this->getIssueTypesIds();
		if (empty($ids)) {
			return true;
		}
		return in_array($typeID, $ids, true);
	}

	public function isForIssueUser(string $type): bool {
		return $this->getIssueUserType() === $type;
	}

	public function getNameWithValue(): string {
		return $this->name . ' - ' . $this->getFormattedValue();
	}

	public static function findCalculationTypes(IssuePayCalculation $calculation, string $issueUserType = null): array {
		return array_filter(static::getTypes(), static function (ProvisionType $provisionType) use ($calculation, $issueUserType) {
			return $provisionType->isForCalculationType($calculation->type)
				&& $provisionType->isForIssueType($calculation->issue->type_id)
				&& (!$issueUserType ? true : $provisionType->isForIssueUser($issueUserType));
		});
	}

	public static function getTypesNames(): array {
		return ArrayHelper::map(static::getTypes(), 'id', 'nameWithValue');
	}

	/**
	 * @return static[]
	 */
	public static function getTypes(): array {
		if (static::$TYPES === null) {
			static::$TYPES = static::find()
				->indexBy('id')
				->all();
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
