<?php

namespace app\models;

use common\models\issue\Issue;
use common\models\User;
use Yii;

/**
 * This is the model class for table "issue_user".
 *
 * @property int $user_id
 * @property int $issue_id
 * @property string $type
 *
 * @property Issue $issue
 * @property User $user
 */
class IssueUser extends \yii\db\ActiveRecord {

	public const TYPE_LAWYER = User::ROLE_LAWYER;
	public const TYPE_AGENT = User::ROLE_AGENT;
	public const TYPE_CLIENT = User::ROLE_CLIENT;
	public const TYPE_VICTIM = User::ROLE_VICTIM;
	public const TYPE_TELEMARKETER = User::ROLE_TELEMARKETER;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName() {
		return 'issue_user';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules() {
		return [
			[['user_id', 'issue_id', 'type'], 'required'],
			[['user_id', 'issue_id'], 'integer'],
			[['type'], 'string', 'max' => 255],
			[['user_id', 'issue_id'], 'unique', 'targetAttribute' => ['user_id', 'issue_id']],
			[['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::class, 'targetAttribute' => ['issue_id' => 'id']],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			['type', 'in', 'range' => array_keys($this->getTypesNames())],
		];
	}

	public static function getTypesNames(): array {
		return [
			static::TYPE_LAWYER => 'Prawnik',
			static::TYPE_AGENT => 'Agent',
			static::TYPE_CLIENT => 'Klient',
			static::TYPE_VICTIM => 'Ofiara',
			static::TYPE_TELEMARKETER => 'Telemarketer',
		];
	}

	public function getTypeName(): string{
		return static::getTypesNames()[$this->type];
	}


	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels() {
		return [
			'user_id' => 'User ID',
			'issue_id' => 'Issue ID',
			'type' => 'Type',
		];
	}

	/**
	 * Gets query for [[Issue]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getIssue() {
		return $this->hasOne(Issue::className(), ['id' => 'issue_id']);
	}

	/**
	 * Gets query for [[User]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser() {
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}
}
