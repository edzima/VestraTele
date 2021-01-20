<?php

namespace common\models\issue;

use common\models\issue\query\IssuePayCalculationQuery;
use common\models\issue\query\IssuePayQuery;
use common\models\issue\query\IssueQuery;
use common\models\provision\Provision;
use common\models\provision\ProvisionQuery;
use common\models\settlement\PayInterface;
use common\models\user\User;
use DateTime;
use Decimal\Decimal;
use Yii;
use yii\base\InvalidCallException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "issue_pay_calculation".
 *
 * @property int $id
 * @property int $issue_id
 * @property int $type
 * @property string $value
 * @property string $details
 * @property string $created_at
 * @property string $updated_at
 * @property string $payment_at
 * @property int $provider_type
 * @property int $stage_id
 * @property int $owner_id
 * @property int $provider_id
 * @property int $problem_status
 * @property bool $is_provider_notified
 *
 * @property-read bool $hasCosts
 *
 * @property-read User $owner
 * @property-read Issue $issue
 * @property-read IssueCost[] $costs
 * @property-read IssuePay[] $pays
 * @property-read IssueStage $stage
 */
class IssuePayCalculation extends ActiveRecord implements PayInterface, IssueInterface {

	use IssueTrait;

	public const TYPE_ADMINISTRATIVE = 10;
	public const TYPE_HONORARIUM = 30;
	public const TYPE_LAWYER = 40;
	public const TYPE_SUBSCRIPTION = 50;

	public const PROBLEM_STATUS_PREPEND_DEMAND = 10;
	public const PROBLEM_STATUS_DEMAND = 15;
	public const PROBLEM_STATUS_PREPEND_JUDGEMENT = 20;
	public const PROBLEM_STATUS_JUDGEMENT = 25;
	public const PROBLEM_STATUS_BAILLIF = 40;

	public const PROVIDER_CLIENT = 1;
	public const PROVIDER_RESPONSIBLE_ENTITY = 10;

	private static ?array $STAGES_NAMES = null;
	private static ?array $OWNER_NAMES = null;

	private static array $USER_PROVISIONS_SUM = [];

	public function afterSave($insert, $changedAttributes): void {
		parent::afterSave($insert, $changedAttributes);
		$this->issue->markAsUpdate();
	}

	public function afterDelete() {
		parent::afterDelete();
		$this->issue->markAsUpdate();
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%issue_pay_calculation}}';
	}

	public static function viaCostTableName(): string {
		return '{{%issue_calculation_cost}}';
	}

	public function behaviors(): array {
		return [
			[
				'class' => TimestampBehavior::class,
				'value' => new Expression('CURRENT_TIMESTAMP'),
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['created_at', 'updated_at', 'payment_at'], 'safe'],
			[['issue_id', 'value'], 'required'],
			[['issue_id'], 'integer'],
			['value', 'number', 'min' => 1, 'numberPattern' => '/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/'],
			[['details'], 'string'],
			[['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::class, 'targetAttribute' => ['issue_id' => 'id']],
			[['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['owner_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'issue_id' => Yii::t('common', 'Issue'),
			'created_at' => Yii::t('common', 'Created at'),
			'updated_at' => Yii::t('common', 'Updated at'),
			'payment_at' => Yii::t('common', 'Payment at'),
			'value' => Yii::t('common', 'Value with VAT'),
			'valueToPay' => Yii::t('common', 'Value to pay'),
			'type' => Yii::t('common', 'Type'),
			'stage_id' => Yii::t('common', 'Stage'),
			'stageName' => Yii::t('common', 'Stage'),
			'typeName' => Yii::t('common', 'Type'),
			'details' => Yii::t('common', 'Details'),
			'providerName' => Yii::t('settlement', 'Provider name'),
			'problemStatusName' => Yii::t('settlement', 'Problem'),
			'owner_id' => Yii::t('common', 'Owner'),
			'owner' => Yii::t('common', 'Owner'),
			'userProvisionsSum' => Yii::t('common', 'User provision (total)'),
			'userProvisionsSumNotPay' => Yii::t('common', 'User provision (not pay)'),
			'is_provider_notified' => Yii::t('common', 'Is provider notified'),
			'costsSum' => Yii::t('settlement', 'Costs sum'),
			'valueWithoutCosts' => Yii::t('settlement', 'Value without costs'),
		];
	}

	public function getName(): string {
		return Yii::t('settlement', 'Settlement {type}', ['type' => $this->getTypeName()]);
	}

	public function getHasCosts(): bool {
		return !empty($this->costs);
	}

	public function getValueWithoutCosts(): Decimal {
		$costs = $this->getCostsSum();
		if ($costs === null) {
			return $this->getValue();
		}
		return $this->getValue()->sub($costs);
	}

	public function getCostsSum(bool $withVAT = false): Decimal {
		if ($this->getHasCosts()) {
			return IssueCost::sum($this->costs, $withVAT);
		}
		return new Decimal(0);
	}

	public function getNotPayedPays(): IssuePayQuery {
		return $this->getPays()->onlyNotPayed();
	}

	public function getValueToPay(): Decimal {
		return $this->getValue()->sub(Yii::$app->pay->payedSum($this->pays));
	}

	public function getValue(): Decimal {
		return new Decimal($this->value);
	}

	public function getPaymentAt(): ?DateTime {
		return new DateTime($this->payment_at);
	}

	public function getUserProvisionsSum(int $id): Decimal {
		if (!isset(static::$USER_PROVISIONS_SUM[$id])) {
			$sum = (string) $this->getPays()
				->joinWith([
					'provisions PR' => function (ProvisionQuery $query) use ($id): void {
						$query->user($id);
					},
				])
				->sum('PR.value');
			if (empty($sum)) {
				$sum = 0;
			}
			static::$USER_PROVISIONS_SUM[$id] = new Decimal($sum);
		}
		return static::$USER_PROVISIONS_SUM[$id];
	}

	public function isForUser(int $id): bool {
		return $this->owner_id === $id ||
			$this->issue->isForUser($id);
	}

	public function getUserProvisionsSumNotPay(int $id): Decimal {
		if (!$this->getUserProvisionsSum($id)->isPositive()) {
			return new Decimal(0);
		}
		$sum = (string) $this->getNotPayedPays()
			->joinWith([
				'provisions PR' => function (ProvisionQuery $query) use ($id): void {
					$query->user($id);
				},
			])
			->sum('PR.value');
		if (empty($sum)) {
			$sum = 0;
		}
		return new Decimal($sum);
	}

	public function getCosts(): ActiveQuery {
		return $this->hasMany(IssueCost::class, ['id' => 'cost_id'])
			->viaTable(static::viaCostTableName(), ['settlement_id' => 'id']);
	}

	/** @noinspection PhpIncompatibleReturnTypeInspection */
	public function getIssue(): IssueQuery {
		return $this->hasOne(Issue::class, ['id' => 'issue_id']);
	}

	public function getOwner(): ActiveQuery {
		return $this->hasOne(User::class, ['id' => 'owner_id']);
	}

	/** @noinspection PhpIncompatibleReturnTypeInspection */
	public function getPays(): IssuePayQuery {
		return $this->hasMany(IssuePay::class, ['calculation_id' => 'id']);
	}

	/** @noinspection PhpIncompatibleReturnTypeInspection */
	public function getProvisions(): ProvisionQuery {
		return $this->hasMany(Provision::class, ['pay_id' => 'id'])->via('pays');
	}

	public function getStage(): ActiveQuery {
		return $this->hasOne(IssueStage::class, ['id' => 'stage_id']);
	}

	public function getTypeName(): string {
		return static::getTypesNames()[$this->type] ?? '';
	}

	public function isPayed(): bool {
		if ($this->isNewRecord) {
			return false;
		}

		return $this->getValueToPay()->isZero();
	}

	public function getDecimalValue(): Decimal {
		return new Decimal($this->value);
	}

	public function getPaysCount(): int {
		return $this->getPays()->count();
	}

	public function getNotPayedPaysCount(): int {
		return $this->getNotPayedPays()->count();
	}

	/** @noinspection PhpUnused */
	public function getProviderName(): ?string {
		return $this->getProvidersNames()[$this->provider_type] ?? null;
	}

	public function getProvidersNames(): array {
		return [
			static::PROVIDER_CLIENT => Yii::t('settlement', 'Customer - {fullName}', [
				'fullName' => $this->issue->customer->getFullName(),
			]),
			static::PROVIDER_RESPONSIBLE_ENTITY => Yii::t('settlement', 'Entity rensponsible - {name}', [
				'name' => $this->issue->entityResponsible,
			]),
		];
	}

	public function hasProblemStatus(): bool {
		return !empty($this->problem_status);
	}

	public function hasPays(): bool {
		return $this->getPaysCount() > 0;
	}

	public function getProblemStatusName(): ?string {
		if (empty($this->problem_status)) {
			return null;
		}
		return static::getProblemStatusesNames()[$this->problem_status];
	}

	public static function getProvidersTypesNames(): array {
		return [
			static::PROVIDER_CLIENT => Yii::t('settlement', 'Customer'),
			static::PROVIDER_RESPONSIBLE_ENTITY => Yii::t('settlement', 'Entity responsible'),
		];
	}

	public static function getTypesNames(): array {
		return [
			static::TYPE_HONORARIUM => Yii::t('settlement', 'Honorarium'),
			static::TYPE_ADMINISTRATIVE => Yii::t('settlement', 'Administrative'),
			static::TYPE_LAWYER => Yii::t('settlement', 'Lawyer'),
			static::TYPE_SUBSCRIPTION => Yii::t('settlement', 'Subscription'),
		];
	}

	public static function getProblemStatusesNames(): array {
		return [
			static::PROBLEM_STATUS_PREPEND_DEMAND => Yii::t('settlement', 'Prepariation for demand'),
			static::PROBLEM_STATUS_DEMAND => Yii::t('settlement', 'Demand'),
			static::PROBLEM_STATUS_PREPEND_JUDGEMENT => Yii::t('settlement', 'Prepariation for judgement'),
			static::PROBLEM_STATUS_JUDGEMENT => Yii::t('settlement', 'Judgement'),
			static::PROBLEM_STATUS_BAILLIF => Yii::t('settlement', 'Baillif'),

		];
	}

	public function getStageName(): string {
		return static::getStagesNames()[$this->stage_id];
	}

	public static function getStagesNames(): array {
		if (static::$STAGES_NAMES === null) {
			$ids = self::find()
				->groupBy('stage_id')
				->select('stage_id')
				->column();
			$models = IssueStage::find()
				->andWhere(['id' => $ids])
				->asArray()
				->all();
			static::$STAGES_NAMES = ArrayHelper::map($models, 'id', 'name');
		}
		return static::$STAGES_NAMES;
	}

	public static function getOwnerNames(): array {
		if (static::$OWNER_NAMES === null) {
			$ids = self::find()
				->groupBy('owner_id')
				->select('owner_id')
				->column();
			$models = User::find()
				->andWhere(['id' => $ids])
				->with('userProfile')
				->all();
			static::$OWNER_NAMES = ArrayHelper::map($models, 'id', 'fullName');
		}
		return static::$OWNER_NAMES;
	}

	public static function find(): IssuePayCalculationQuery {
		return new IssuePayCalculationQuery(static::class);
	}

	public function getDeadlineAt(): ?DateTime {
		// TODO: Implement getDeadlineAt() method.
	}

	public function getVAT(): ?Decimal {
		// TODO: Implement getVAT() method.
	}

	public function getTransferType(): int {
		// TODO: Implement getTransferType() method.
	}

	public function isDelayed(string $range = 'now'): bool {
		foreach ($this->pays as $pay) {
			if ($pay->isDelayed($range)) {
				return true;
			}
		}
		return false;
	}

	public function markAsPayment(DateTime $dateTime): void {
		foreach ($this->pays as $pay) {
			if (!$pay->isPayed()) {
				$pay->markAsPayment($dateTime);
			}
		}
	}

	public function unlinkCosts(): void {
		$this->unlinkAll('costs', true);
	}

	public function linkCosts(array $costs_ids): int {
		if ($this->isNewRecord) {
			throw new InvalidCallException('Unable to link costs: the model being linked cannot be newly created.');
		}
		$rows = [];
		foreach ($costs_ids as $id) {
			$rows[] = [
				'settlement_id' => $this->id,
				'cost_id' => $id,
			];
		}
		return static::getDb()->createCommand()
			->batchInsert(static::viaCostTableName(), ['settlement_id', 'cost_id'], $rows)->execute();
	}
}
