<?php

namespace common\models\issue;

use common\models\issue\query\IssueCostQuery;
use common\models\issue\query\IssuePayCalculationQuery;
use common\models\issue\query\IssueQuery;
use common\models\settlement\CostType;
use common\models\settlement\VATInfoTrait;
use common\models\user\query\UserQuery;
use common\models\user\User;
use Decimal\Decimal;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "issue_cost".
 *
 * @property int $id
 * @property int $type_id
 * @property int|null $issue_id
 * @property string $value
 * @property string|null $vat
 * @property int $created_at
 * @property int $updated_at
 * @property string $date_at
 * @property string $deadline_at
 * @property string|null $transfer_type
 * @property string|null $settled_at
 * @property string|null $confirmed_at
 * @property int|null $user_id
 * @property int|null $hide_on_report
 * @property int|null $creator_id
 *
 * @property-read bool $is_confirmed
 * @property-read string|null $transferTypeName
 * @property-read string $typeName
 * @property-read string $typeNameWithValue
 * @property-read bool $isSettled
 * @property-read bool $hasSettlements
 *
 * @property-read CostType $type
 * @property-read Issue|null $issue
 * @property-read IssuePayCalculation[] $settlements
 * @property-read User|null $user
 * @property-read User|null $creator
 */
class IssueCost extends ActiveRecord implements IssueCostInterface {

	use VATInfoTrait;

	public static function tableName(): string {
		return '{{%issue_cost}}';
	}

	public function behaviors(): array {
		return [
			TimestampBehavior::class,
		];
	}

	public function getTypeNameWithValue(): string {
		return $this->getTypeName()
			. ' - '
			. Yii::$app->formatter->asCurrency($this->getValueWithVAT());
	}

	public function getType(): CostType {
		return $this->costType;
	}

	public function getCostType(): ActiveQuery {
		return $this->hasOne(CostType::class, ['id' => 'type_id']);
	}

	public function getTypeName(): string {
		return $this->getType()->name;
	}

	public function getTypeNameWithId(): string {
		return $this->getTypeName() . ': #' . $this->id;
	}

	public function getTransferTypeName(): ?string {
		return static::getTransfersTypesNames()[$this->transfer_type] ?? null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return array_merge($this->vatAttributeLabels(), [
			'id' => 'ID',
			'issue_id' => Yii::t('common', 'Issue'),
			'type_id' => Yii::t('common', 'Type'),
			'typeName' => Yii::t('common', 'Type'),
			'value' => Yii::t('common', 'Value with VAT'),
			'vat' => 'VAT (%)',
			'VATPercent' => 'VAT (%)',
			'created_at' => Yii::t('common', 'Created at'),
			'updated_at' => Yii::t('common', 'Updated at'),
			'confirmed_at' => Yii::t('settlement', 'Confirmed at'),
			'creator' => Yii::t('settlement', 'Creator'),
			'date_at' => Yii::t('settlement', 'Date at'),
			'deadline_at' => Yii::t('settlement', 'Deadline at'),
			'settled_at' => Yii::t('common', 'Settled at'),
			'user_id' => Yii::t('common', 'User'),
			'user' => Yii::t('common', 'User'),
			'transfer_type' => Yii::t('settlement', 'Transfer Type'),
			'transferTypeName' => Yii::t('settlement', 'Transfer Type'),
			'is_confirmed' => Yii::t('settlement', 'Is Confirmed'),
			'hide_on_report' => Yii::t('provision', 'Hide on report'),
		]);
	}

	/** @noinspection PhpIncompatibleReturnTypeInspection */
	public function getUser(): UserQuery {
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	/** @noinspection PhpIncompatibleReturnTypeInspection */
	public function getCreator(): UserQuery {
		return $this->hasOne(User::class, ['id' => 'creator_id']);
	}

	/** @noinspection PhpIncompatibleReturnTypeInspection */
	public function getIssue(): IssueQuery {
		return $this->hasOne(Issue::class, ['id' => 'issue_id']);
	}

	/** @noinspection PhpIncompatibleReturnTypeInspection */
	public function getSettlements(): IssuePayCalculationQuery {
		return $this->hasMany(IssuePayCalculation::class, ['id' => 'settlement_id'])
			->viaTable(IssuePayCalculation::viaCostTableName(), ['cost_id' => 'id']);
	}

	public function getTransferType(): ?string {
		return $this->transfer_type;
	}

	public function getValue(): Decimal {
		return new Decimal($this->value);
	}

	public function getIsSettled(): bool {
		return !empty($this->settled_at);
	}

	public function getIsConfirmed(): bool {
		return $this->confirmed_at !== null;
	}

	public function getHasSettlements(): bool {
		return !empty($this->settlements);
	}

	public function isForUser(int $userId): bool {
		return (int) $this->user_id === $userId;
	}

	public function hasUser(): bool {
		return $this->user !== null;
	}

	public function unlinkSettlement(int $settlementId): void {
		static::getDb()
			->createCommand()
			->delete(IssuePayCalculation::viaCostTableName(), ['cost_id' => $this->id, 'settlement_id' => $settlementId])
			->execute();
	}

	public static function getTransfersTypesNames(): array {
		return [
			static::TRANSFER_TYPE_CASH => Yii::t('settlement', 'Cash'),
			static::TRANSFER_TYPE_BANK => Yii::t('settlement', 'Bank Transfer'),
		];
	}

	/**
	 * @param static[] $costs
	 * @return static[]
	 */
	public static function userFilter(array $costs, int $userId): array {
		return array_filter($costs, static function (IssueCost $cost) use ($userId): bool {
			return $cost->isForUser($userId);
		});
	}

	/**
	 * @param static[] $costs
	 * @return static[]
	 */
	public static function withoutUserFilter(array $costs, int $userId): array {
		return array_filter($costs, static function (IssueCost $cost) use ($userId): bool {
			if ($userId) {
				return $cost->user_id !== $userId;
			}
			return $cost->user_id !== null;
		});
	}

	/**
	 * @param static[] $costs
	 * @return Decimal
	 */
	public static function sum(array $costs, bool $withVAT = false): Decimal {
		$sum = new Decimal(0);
		foreach ($costs as $cost) {
			$value = $withVAT ? $cost->getValueWithVAT() : $cost->getValueWithoutVAT();
			$sum = $sum->add($value);
		}
		return $sum;
	}

	/**
	 * @inheritdoc
	 * @return IssueCostQuery the active query used by this AR class.
	 */
	public static function find(): IssueCostQuery {
		return new IssueCostQuery(static::class);
	}

}
