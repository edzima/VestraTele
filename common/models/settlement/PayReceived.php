<?php

namespace common\models\settlement;

use common\models\issue\Issue;
use common\models\issue\IssueInterface;
use common\models\issue\IssuePay;
use common\models\issue\IssueTrait;
use common\models\user\User;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "pay_received".
 *
 * @property int $pay_id
 * @property int $user_id
 * @property string $date_at
 * @property string|null $transfer_at
 *
 * @property IssuePay $pay
 * @property User $user
 */
class PayReceived extends ActiveRecord implements IssueInterface {

	use IssueTrait;

	public function getIssueId(): int {
		return $this->getIssueModel()->id;
	}

	public function getIssueModel(): Issue {
		return $this->pay->calculation->issue;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return 'pay_received';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['user_id'], 'required'],
			[['user_id'], 'integer'],
			[['date_at', 'transfer_at'], 'safe'],
			[['pay_id'], 'exist', 'skipOnError' => true, 'targetClass' => IssuePay::class, 'targetAttribute' => ['pay_id' => 'id']],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'pay_id' => Yii::t('settlement', 'Pay ID'),
			'user_id' => Yii::t('settlement', 'Receiver'),
			'date_at' => Yii::t('settlement', 'Receive At'),
			'transfer_at' => Yii::t('settlement', 'Transfer At'),
		];
	}

	/**
	 * Gets query for [[Pay]].
	 *
	 * @return ActiveQuery
	 */
	public function getPay() {
		return $this->hasOne(IssuePay::class, ['id' => 'pay_id']);
	}

	/**
	 * Gets query for [[User]].
	 *
	 * @return ActiveQuery
	 */
	public function getUser() {
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

}
