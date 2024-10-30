<?php

namespace common\models\settlement;

use common\components\rbac\ModelAccessManager;
use common\components\rbac\ModelRbacInterface;
use common\components\rbac\SettlementTypeAccessManager;
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
 *
 * @property IssuePayCalculation[] $issuePayCalculations
 * @property IssueType[] $issueTypes
 */
class SettlementType extends ActiveRecord implements ModelRbacInterface {

	private ?SettlementTypeOptions $typeOptions = null;

	protected const ISSUE_TYPE_VIA_TABLE = '{{%settlement_type_issue_type}}';

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%settlement_type}}';
	}

	private static ?array $MODELS = null;

	public static function getNames(): array {
		return ArrayHelper::map(
			static::getModels(),
			'id',
			'name'
		);
	}

	/**
	 * @param bool $refresh
	 * @return static[] indexed by id
	 */
	public static function getModels(bool $refresh = false): array {
		if (static::$MODELS === null || $refresh) {
			static::$MODELS = static::find()
				->indexBy('id')
				->all();
		}
		return static::$MODELS;
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['name'], 'required'],
			[['is_active'], 'integer'],
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
		];
	}

	public function linkIssueTypes(array $typesIds, bool $withUnlink = true): void {
		if ($withUnlink) {
			$this->unlinkAll('issueTypes', true);
		}
		$rows = [];
		foreach ($typesIds as $id) {
			if (!empty($id)) {
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

	public function hasAccess(string|int $id, string $action = SettlementTypeAccessManager::ACTION_ISSUE_VIEW, string $app = null): bool {
		$rbac = $this->getModelAccess();
		$rbac->setAction($action);
		if ($app) {
			$rbac->setApp($app);
		}
		return $rbac->checkAccess($id);
	}

	public function isForIssueTypeId(int $type_id): bool {
		if (empty($this->issueTypes)) {
			return true;
		}
		foreach ($this->issueTypes as $issueType) {
			if ($issueType->id === $type_id || in_array($type_id, $issueType->getAllChildesIds())) {
				return true;
			}
		}
		return false;
	}

	public function getRbacBaseName(): string {
		return 'settlement_types';
	}

	public function getRbacId(): ?string {
		return $this->id;
	}

	public function getModelAccess(): ModelAccessManager {
		return Yii::$app->accessManagerFactory
			->getManager(static::class)
			->setModel($this);
	}

	public static function find(): SettlementTypeQuery {
		return new SettlementTypeQuery(static::class);
	}

}
