<?php

namespace common\models\settlement;

use common\helpers\ArrayHelper;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueType;
use common\models\settlement\query\SettlementTypeQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * This is the model class for table "settlement_type".
 *
 * @property int $id
 * @property string $name
 * @property int|null $is_active
 * @property string|null $options
 * @property int $visibility_status
 *
 * @property IssuePayCalculation[] $issuePayCalculations
 * @property IssueType[] $issueTypes
 */
class SettlementType extends ActiveRecord {

	public const VISIBILITY_ONLY_OWNER = 10;
	public const VISIBILITY_OLNY_BOOKEEPER = 20;
	public const VISIBILITY_ISSUE_USERS = 30;
	public const VISIBILITY_ISSUE_ACCESS = 40;

	private ?SettlementTypeOptions $typeOptions = null;

	protected const ISSUE_TYPE_VIA_TABLE = '{{%settlement_type_issue_type}}';

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%settlement_type}}';
	}

	public static function findForIssueType(int $typeId): array {
		return static::find()
			->forIssueTypes([$typeId])
			->all();
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['name'], 'required'],
			[['is_active', 'visibility_status'], 'integer'],
			[['options'], 'safe'],
			[['name'], 'string', 'max' => 255],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('settlement', 'ID'),
			'name' => Yii::t('settlement', 'Name'),
			'is_active' => Yii::t('settlement', 'Is Active'),
			'issueTypes' => Yii::t('settlement', 'Issue Types'),
			'options' => Yii::t('settlement', 'Options'),
			'visibility_status' => Yii::t('settlement', 'Visibility'),
			'visibilityName' => Yii::t('settlement', 'Visibility'),
		];
	}

	public function linkIssueTypes(array $typesIds, bool $withUnlink = true): void {
		if ($withUnlink) {
			$this->unlinkAll('issueTypes', true);
		}
		$rows = [];
		foreach ($typesIds as $id) {
			if (is_int($id)) {
				$rows[] = [
					'settlement_type_id' => $this->id,
					'issue_type_id' => $id,
				];
			}
		}
		if (!empty($rows)) {
			$this->getDb()
				->createCommand()
				->batchInsert(static::ISSUE_TYPE_VIA_TABLE, ['settlement_type_id', 'issue_type_id'], $rows)->execute();
		}
	}

	/**
	 * Gets query for [[IssuePayCalculations]].
	 *
	 * @return ActiveQuery
	 */
	public function getIssuePayCalculations() {
		return $this->hasMany(IssuePayCalculation::class, ['type' => 'id']);
	}

	public function getIssueTypes() {
		return $this->hasMany(IssueType::class, ['id' => 'issue_type_id'])
			->viaTable(static::ISSUE_TYPE_VIA_TABLE, ['settlement_type_id' => 'id']);
	}

	public function getVisibilityName(): ?string {
		return static::visibilityNames()[$this->visibility_status] ?? null;
	}

	public function getTypeOptions(): SettlementTypeOptions {
		if ($this->typeOptions === null) {
			$attributes = $this->options;
			if (!is_array($attributes)) {
				$attributes = Json::decode($attributes);
			}
			$options = new SettlementTypeOptions($attributes);
			$this->typeOptions = $options;
		}
		return $this->typeOptions;
	}

	public function setTypeOptions(SettlementTypeOptions $model): void {
		$this->typeOptions = $model;
	}

	public function getIssueTypesIds(): ?array {
		return ArrayHelper::getColumn($this->issueTypes, 'id');
	}

	public static function visibilityNames(): array {
		return [
			static::VISIBILITY_ONLY_OWNER => Yii::t('settlement', 'Visibility: Owner'),
			static::VISIBILITY_OLNY_BOOKEEPER => Yii::t('settlement', 'Visibility: Bookeper'),
			static::VISIBILITY_ISSUE_USERS => Yii::t('settlement', 'Visibility: Issue Users'),
			static::VISIBILITY_ISSUE_ACCESS => Yii::t('settlement', 'Visibility: Issue Access'),
		];
	}

	public static function find(): SettlementTypeQuery {
		return new SettlementTypeQuery(static::class);
	}

}
