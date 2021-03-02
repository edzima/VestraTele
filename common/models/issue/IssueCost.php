<?php

namespace common\models\issue;

use common\models\issue\query\IssueCostQuery;
use common\models\issue\query\IssuePayCalculationQuery;
use common\models\issue\query\IssueQuery;
use common\models\settlement\VATInfo;
use common\models\settlement\VATInfoTrait;
use common\models\user\query\UserQuery;
use common\models\user\User;
use Decimal\Decimal;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "issue_cost".
 *
 * @property int $id
 * @property int $issue_id
 * @property string $type
 * @property string $value
 * @property string|null $vat
 * @property int $created_at
 * @property int $updated_at
 * @property string $date_at
 * @property int|null $user_id
 *
 * @property-read string $typeName
 * @property-read string $typeNameWithValue
 *
 * @property-read Issue $issue
 * @property-read IssuePayCalculation[] $settlements
 * @property-read User|null $user
 */
class IssueCost extends ActiveRecord implements
	IssueInterface, VATInfo {

	use IssueTrait;
	use VATInfoTrait;

	public const TYPE_COURT_ENTRY = 'court_entry';
	public const TYPE_POWER_OF_ATTORNEY = 'power_of_attorney';
	public const TYPE_PURCHASE_OF_RECEIVABLES = 'purchase_of_receivables';
	public const TYPE_WRIT = 'writ';
	public const TYPE_OFFICE = 'office';
	public const TYPE_JUSTIFICATION_OF_THE_JUDGMENT = 'justification_of_the_judgment';
	public const TYPE_INSTALLMENT = 'installment';

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

	public function getTypeName(): string {
		return static::getTypesNames()[$this->type];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'issue_id' => Yii::t('common', 'Issue'),
			'type' => Yii::t('common', 'Type'),
			'typeName' => Yii::t('common', 'Type'),
			'value' => Yii::t('common', 'Value with VAT'),
			'vat' => 'VAT (%)',
			'VATPercent' => 'VAT (%)',
			'created_at' => Yii::t('common', 'Created at'),
			'updated_at' => Yii::t('common', 'Updated at'),
			'date_at' => Yii::t('common', 'Date at'),
			'user' => Yii::t('common', 'User'),
		];
	}

	/** @noinspection PhpIncompatibleReturnTypeInspection */
	public function getUser(): UserQuery {
		return $this->hasOne(User::class, ['id' => 'user_id']);
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

	public function getHasSettlements(): bool {
		return !empty($this->settlements);
	}

	public static function getTypesNames(): array {
		return [
			static::TYPE_PURCHASE_OF_RECEIVABLES => Yii::t('common', 'Purchase of receivables'),
			static::TYPE_COURT_ENTRY => Yii::t('common', 'Court entry'),
			static::TYPE_POWER_OF_ATTORNEY => Yii::t('common', 'Power of attorney'),
			static::TYPE_OFFICE => Yii::t('common', 'Office'),
			static::TYPE_WRIT => Yii::t('common', 'Writ'),
			static::TYPE_JUSTIFICATION_OF_THE_JUDGMENT => Yii::t('common', 'Justification of the judgment'),
			static::TYPE_INSTALLMENT => Yii::t('common', 'Installment'),
		];
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
	 * @return IssueQuery the active query used by this AR class.
	 */
	public static function find(): IssueCostQuery {
		return new IssueCostQuery(static::class);
	}

	public function unlinkSettlement(int $settlementId): void {
		static::getDb()
			->createCommand()
			->delete(IssuePayCalculation::viaCostTableName(), ['cost_id' => $this->id, 'settlement_id' => $settlementId])
			->execute();
	}

}
