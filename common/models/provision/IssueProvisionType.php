<?php

namespace common\models\provision;

use backend\modules\issue\models\IssueStage;
use common\models\issue\Issue;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueSettlement;
use common\models\issue\IssueType;
use common\models\issue\IssueUser;
use Decimal\Decimal;
use Yii;
use yii\base\InvalidCallException;
use yii\helpers\ArrayHelper;

class IssueProvisionType extends ProvisionType {

	public const KEY_DATA_CALCULATION_TYPES = 'calculation.types';

	private const KEY_DATA_ISSUE_STAGES = 'issue.stages';
	private const KEY_DATA_ISSUE_TYPES = 'issue.types';
	private const KEY_DATA_ISSUE_REQUIRED_USER_TYPES = 'issue.user.types.required';
	public const KEY_DATA_ISSUE_USER_TYPE = 'issue.user.type';
	public const KEY_DATA_ISSUE_EXCLUDED_USER_TYPES = 'issue.user.types.excluded';
	public const KEY_DATA_SETTLEMENT_DATE = 'dateFromSettlement';

	public const KEY_DATA_MIN_SETTLEMENT_VALUE = 'minSettlementValue';
	public const KEY_DATA_MAX_SETTLEMENT_VALUE = 'maxSettlementValue';

	public static function settlementTypesNames(): array {
		return IssuePayCalculation::getTypesNames();
	}

	public static function issueStagesNames(): array {
		return IssueStage::getStagesNames();
	}

	public static function issueTypesNames(): array {
		return IssueType::getTypesNames();
	}

	public static function issueUserTypesNames(): array {
		return IssueUser::getTypesNames();
	}

	public function attributeLabels(): array {
		return array_merge(parent::attributeLabels(), [
			'settlementTypesNames' => Yii::t('settlement', 'Settlement type'),
			'issueRequiredUserTypesNames' => Yii::t('common', 'Issue required user types'),
			'issueTypesNames' => Yii::t('common', 'Issue Types'),
			'issueStagesNames' => Yii::t('common', 'Issue Stages'),
			'issueUserTypeName' => Yii::t('common', 'Issue user type'),
			'issueExcludedUserTypesNames' => Yii::t('provision', 'Excluded Users Types'),
			'isForDateFromSettlement' => Yii::t('provision', 'Date from Settlement'),
			'minSettlementValue' => Yii::t('provision', 'Min Settlement Value'),
			'maxSettlementValue' => Yii::t('provision', 'Max Settlement Value'),
		]);
	}

	public function getIsForDateFromSettlement(): bool {
		return $this->getDataArray()[static::KEY_DATA_SETTLEMENT_DATE] ?? false;
	}

	public function setIsForDateFromSettlement(bool $value): void {
		$this->setDataValues(static::KEY_DATA_SETTLEMENT_DATE, $value);
	}

	public function getSettlementTypes(): array {
		return $this->getDataArray()[static::KEY_DATA_CALCULATION_TYPES] ?? [];
	}

	public function setSettlementTypes(array $types): void {
		$types = array_map('intval', $types);
		$this->setDataValues(static::KEY_DATA_CALCULATION_TYPES, $types);
	}

	public function getIssueStagesIds(): array {
		return $this->getDataArray()[static::KEY_DATA_ISSUE_STAGES] ?? [];
	}

	public function setIssueStagesIds(array $ids): void {
		$ids = array_map('intval', $ids);
		$this->setDataValues(static::KEY_DATA_ISSUE_STAGES, $ids);
	}

	public function getIssueTypesIds(): array {
		return $this->getDataArray()[static::KEY_DATA_ISSUE_TYPES] ?? [];
	}

	public function setIssueTypesIds(array $ids): void {
		$ids = array_map('intval', $ids);
		$this->setDataValues(static::KEY_DATA_ISSUE_TYPES, $ids);
	}

	public function getIssueUserType(): ?string {
		return $this->getDataArray()[static::KEY_DATA_ISSUE_USER_TYPE];
	}

	public function setIssueUserTypes(string $type): void {
		$this->setDataValues(static::KEY_DATA_ISSUE_USER_TYPE, $type);
	}

	public function getIssueRequiredUserTypes(): array {
		return $this->getDataArray()[static::KEY_DATA_ISSUE_REQUIRED_USER_TYPES] ?? [];
	}

	public function setIssueRequiredUserTypes(array $types): void {
		$this->setDataValues(static::KEY_DATA_ISSUE_REQUIRED_USER_TYPES, $types);
	}

	public function getIssueExcludedUserTypes(): array {
		return $this->getDataArray()[static::KEY_DATA_ISSUE_EXCLUDED_USER_TYPES] ?? [];
	}

	public function setIssueExcludedUserTypes(array $types): void {
		$this->setDataValues(static::KEY_DATA_ISSUE_EXCLUDED_USER_TYPES, $types);
	}

	public function getSettlementTypesNames(): string {
		$types = $this->getSettlementTypes();
		if (empty($types)) {
			return Yii::t('common', 'All');
		}
		$allNames = static::settlementTypesNames();
		$names = [];
		foreach ($types as $type) {
			$names[] = $allNames[$type];
		}
		return implode(', ', $names);
	}

	public function getIssueTypesNames(): string {
		$types = $this->getIssueTypesIds();
		if (empty($types)) {
			return Yii::t('common', 'All');
		}
		$typesNames = static::issueTypesNames();
		$names = [];
		foreach ($types as $id) {
			$names[] = $typesNames[$id];
		}
		return implode(', ', $names);
	}

	public function getIssueUserTypeName(): ?string {
		return static::issueUserTypesNames()[$this->getIssueUserType()] ?? null;
	}

	public function getIssueRequiredUserTypesNames(): string {
		return static::getIssueUserTypesNames($this->getIssueRequiredUserTypes());
	}

	public function getIssueExcludedUserTypesNames(): string {
		return static::getIssueUserTypesNames($this->getIssueExcludedUserTypes());
	}

	protected static function getIssueUserTypesNames(array $types): string {
		if (empty($types)) {
			return Yii::t('yii', '(not set)');
		}
		$typesNames = IssueUser::getTypesNames();
		$names = [];
		foreach ($types as $type) {
			$names[] = $typesNames[$type];
		}
		return implode(', ', $names);
	}

	public function isForIssue(Issue $issue): bool {
		return
			$this->hasRequiredIssueUserTypes($issue)
			&& !$this->hasExcludedIssueUserTypes($issue)
			&& $this->isForDate($issue->created_at)
			&& $this->isForIssueStage($issue->stage_id)
			&& $this->isForIssueType($issue->type_id);
	}

	public function hasRequiredIssueUserTypes(Issue $issue = null, array $types = []): bool {
		$requiredTypes = $this->getIssueRequiredUserTypes();
		if (empty($requiredTypes)) {
			return true;
		}
		if (empty($types)) {
			if ($issue === null) {
				throw new InvalidCallException('$types cannot be empty when $issue is null.');
			}
			$types = ArrayHelper::getColumn($issue->users, 'type');
		}
		foreach ($requiredTypes as $requiredType) {
			if (!in_array($requiredType, $types, true)) {
				return false;
			}
		}
		return true;
	}

	public function hasExcludedIssueUserTypes(Issue $issue = null, array $types = []): bool {
		$excludedTypes = $this->getIssueExcludedUserTypes();
		if (empty($excludedTypes)) {
			return false;
		}
		if (empty($types)) {
			if ($issue === null) {
				throw new InvalidCallException('$types cannot be empty when $issue is null.');
			}
			$types = ArrayHelper::getColumn($issue->users, 'type');
		}
		foreach ($excludedTypes as $type) {
			if (in_array($type, $types, true)) {
				return true;
			}
		}
		return false;
	}

	public function isForSettlementType(int $type): bool {
		$types = $this->getSettlementTypes();
		if (empty($types)) {
			return true;
		}
		return in_array($type, $types, true);
	}

	public function isForIssueStage(int $id): bool {
		$ids = $this->getIssueStagesIds();
		if (empty($ids)) {
			return true;
		}
		return in_array($id, $ids, true);
	}

	public function isForIssueType(int $id): bool {
		$ids = $this->getIssueTypesIds();
		if (empty($ids)) {
			return true;
		}
		if (in_array($id, $ids, true)) {
			return true;
		}
		$type = IssueType::getTypes()[$id];
		if ($type->parent_id) {
			return $this->isForIssueType($type->parent_id);
		}
		return false;
	}

	public function isForIssueUser(string $type): bool {
		return $this->getIssueUserType() === $type;
	}

	/**
	 * @param IssueSettlement $settlement
	 * @param string|null $issueUserType
	 * @param bool $onlyActive
	 * @return static[]
	 */
	public static function findSettlementTypes(IssueSettlement $settlement, string $issueUserType = null, bool $onlyActive = true): array {
		return static::settlementFilter(static::getTypes($onlyActive), $settlement, $issueUserType);
	}

	/**
	 * @param static[] $types
	 * @param IssueSettlement $settlement
	 * @param string|null $issueUserType
	 * @return static[]
	 */
	public static function settlementFilter(array $types, IssueSettlement $settlement, string $issueUserType = null): array {
		return static::filter($types, static function (IssueProvisionType $provisionType) use ($settlement, $issueUserType) {
			return $provisionType->isForSettlement($settlement, $issueUserType);
		});
	}

	public function isForSettlement(IssueSettlement $settlement, string $issueUserType = null): bool {
		return $this->isForSettlementType($settlement->getTypeId())
			&& $this->isForIssue($settlement->getIssueModel())
			&& $this->isForValue($settlement->getValue())
			&& (!$issueUserType || $this->isForIssueUser($issueUserType));
	}

	public function getMinSettlementValue(): ?string {
		return $this->getDataArray()[static::KEY_DATA_MIN_SETTLEMENT_VALUE] ?? null;
	}

	public function setMinSettlementValue(?string $value): void {
		$this->setDataValues(static::KEY_DATA_MIN_SETTLEMENT_VALUE, $value);
	}

	public function getMaxSettlementValue(): ?string {
		return $this->getDataArray()[static::KEY_DATA_MAX_SETTLEMENT_VALUE] ?? null;
	}

	public function setMaxSettlementValue(?string $value): void {
		$this->setDataValues(static::KEY_DATA_MAX_SETTLEMENT_VALUE, $value);
	}

	public function isForValue(Decimal $value): bool {
		$min = $this->getMinSettlementValue();
		$max = $this->getMaxSettlementValue();
		if (!$min && !$max) {
			return true;
		}
		if ($min) {
			if ($value < $min) {
				return false;
			}
		}
		if ($max) {
			if ($value > $max) {
				return false;
			}
		}
		return true;
	}

}
