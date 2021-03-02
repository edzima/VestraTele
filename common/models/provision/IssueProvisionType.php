<?php

namespace common\models\provision;

use backend\modules\issue\models\IssueStage;
use common\models\issue\Issue;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueType;
use common\models\issue\IssueUser;
use Yii;
use yii\helpers\ArrayHelper;

class IssueProvisionType extends ProvisionType {

	public const KEY_DATA_CALCULATION_TYPES = 'calculation.types';

	private const KEY_DATA_ISSUE_STAGES = 'issue.stages';
	private const KEY_DATA_ISSUE_TYPES = 'issue.types';
	public const KEY_DATA_ISSUE_USER_TYPE = 'issue.user.type';

	public function attributeLabels(): array {
		return array_merge(parent::attributeLabels(), [
			'only_with_tele' => Yii::t('provision', 'Only with telemarketer'),
			'calculationTypesNames' => Yii::t('settlement', 'Settlement type'),
			'issueTypesNames' => Yii::t('common', 'Issue Types'),
			'issueStagesNames' => Yii::t('common', 'Issue Stages'),
			'issueUserTypeName' => Yii::t('common', 'Issue user type'),
		]);
	}

	public function getCalculationTypes(): array {
		return $this->getDataArray()[static::KEY_DATA_CALCULATION_TYPES] ?? [];
	}

	public function setCalculationTypes(array $types): void {
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

	public function getIssueUserType(): string {
		return $this->getDataArray()[static::KEY_DATA_ISSUE_USER_TYPE];
	}

	public function setIssueUserTypes(string $type): void {
		$this->setDataValues(static::KEY_DATA_ISSUE_USER_TYPE, $type);
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

	public function isForIssue(Issue $issue): bool {
		return
			$this->isForIssueTele($issue)
			&& $this->isForDate($issue->created_at)
			&& $this->isForIssueStage($issue->stage_id)
			&& $this->isForIssueType($issue->type_id);
	}

	public function isForIssueTele(Issue $issue): bool {
		return !($this->only_with_tele && !$issue->hasTele());
	}

	public function isForCalculationType(int $type): bool {
		$types = $this->getCalculationTypes();
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
		return in_array($id, $ids, true);
	}

	public function isForIssueUser(string $type): bool {
		return $this->getIssueUserType() === $type;
	}

	public function isForCalculation(IssuePayCalculation $calculation, string $issueUserType = null) {
		return $this->isForCalculationType($calculation->type)
			&& $this->isForIssue($calculation->issue)
			&& (!$issueUserType ? true : $this->isForIssueUser($issueUserType));
	}

	public static function findCalculationTypes(IssuePayCalculation $calculation, bool $onlyActive = true, string $issueUserType = null): array {
		return ArrayHelper::index(array_filter(static::getTypes($onlyActive, true), static function (ProvisionType $provisionType) use ($calculation, $issueUserType) {
			return $provisionType->isForCalculation($calculation, $issueUserType);
		}), 'id');
	}

	public static function calculationTypesNames(): array {
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

}
