<?php

namespace common\models\provision;

use common\models\issue\Issue;
use common\models\issue\IssueInterface;
use common\models\issue\IssuePay;
use common\models\issue\IssueTrait;
use common\models\user\User;
use Decimal\Decimal;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "provision".
 *
 * @property int $id
 * @property int $pay_id
 * @property string $value
 * @property int $type_id
 * @property int $from_user_id
 * @property int $to_user_id
 * @property int $hide_on_report
 * @property string|null $percent
 *
 * @property-read string $provision
 * @property-read IssuePay $pay
 * @property-read ProvisionType $type
 * @property-read User $toUser
 * @property-read User $fromUser
 */
class Provision extends ActiveRecord implements IssueInterface {

	use IssueTrait;

	public function getIssueId(): int {
		return $this->getIssueModel()->id;
	}

	public function getIssueModel(): Issue {
		return $this->pay->issue;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return 'provision';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['pay_id', 'to_user_id', 'value', 'type_id'], 'required'],
			[['pay_id', 'to_user_id', 'from_user_id'], 'integer'],
			[['value'], 'number'],
			['hide_on_report', 'boolean'],
			[['pay_id'], 'exist', 'skipOnError' => true, 'targetClass' => IssuePay::class, 'targetAttribute' => ['pay_id' => 'id']],
			[['from_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['from_user_id' => 'id']],
			[['to_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['to_user_id' => 'id']],
			[['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProvisionType::class, 'targetAttribute' => ['type_id' => 'id']],

		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'pay_id' => 'Pay ID',
			'to_user_id' => 'Dla',
			'from_user_id' => 'Nadprowizja',
			'value' => Yii::t('provision', 'Provision ({currencySymbol})', ['currencySymbol' => Yii::$app->formatter->getCurrencySymbol()]),
			'toUser' => 'Dla',
			'fromUser' => 'Nadprowizja',
			'fromUserString' => 'Nadprowizja',
			'hide_on_report' => Yii::t('provision', 'Hide on report'),
			'provision' => Yii::t('provision', 'Provision'),
		];
	}

	public function getPay(): ActiveQuery {
		return $this->hasOne(IssuePay::class, ['id' => 'pay_id']);
	}

	public function getFromUser(): ActiveQuery {
		return $this->hasOne(User::class, ['id' => 'from_user_id']);
	}

	public function getToUser(): ActiveQuery {
		return $this->hasOne(User::class, ['id' => 'to_user_id']);
	}

	public function getType(): ActiveQuery {
		return $this->hasOne(ProvisionType::class, ['id' => 'type_id']);
	}

	public function getFromUserString(): string {
		return $this->from_user_id
		&& $this->to_user_id !== $this->from_user_id
		&& $this->fromUser ? $this->fromUser : '';
	}

	public function getValue(): Decimal {
		return new Decimal($this->value);
	}

	public function getPercent(): ?Decimal {
		if ($this->percent) {
			return new Decimal($this->percent);
		}
		if ($this->type->is_percentage) {
			return $this->getValue()->div(Yii::$app->provisions->issuePayValue($this->pay))->mul(100);
		}
		return null;
	}

	public function getProvision(): string {
		if ($this->percent) {
			return Yii::$app->formatter->asPercent($this->percent / 100);
		}
		return Yii::$app->formatter->asCurrency($this->getValue());
	}

	public static function find(): ProvisionQuery {
		return new ProvisionQuery(static::class);
	}
}
