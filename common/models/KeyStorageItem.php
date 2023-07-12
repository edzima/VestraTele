<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%key_storage_item}}".
 *
 * @property int $id
 * @property string $key
 * @property string $value
 * @property string $comment
 */
class KeyStorageItem extends ActiveRecord {

	public const KEY_BACKEND_THEME_SKIN = 'backend.theme-skin';

	public const KEY_FRONTEND_REGISTRATION = 'frontend.registration';
	public const KEY_FRONTEND_EMAIL_CONFIRM = 'frontend.email-confirm';

	public const KEY_ROBOT_SMS_OWNER_ID = 'robot-sms-owner-id';
	public const KEY_SETTLEMENT_TYPES_FOR_PROVISIONS = 'provisions.settlement.types';
	public const KEY_ISSUE_CUSTOMER_DEFAULT_SMS_MESSAGE = 'issue.sms.customer';
	public const KEY_ISSUE_AGENT_DEFAULT_SMS_MESSAGE = 'issue.sms.agent';
	public const KEY_CALENDAR_USERS_BACKGROUND = 'calendar.users.background';
	public const KEY_ISSUE_DEEP_ARCHIVE_DAYS = 'issue.deep_archive_days';
	public const KEY_LEAD_CUSTOMER_SOURCE = 'lead.customer.source';
	public const KEY_EMAIL_BACKGROUND = 'email.background';
	public const KEY_EMAIL_LOGO_URL = 'email.logoUrl';

	/**
	 * @inheritdoc
	 */
	public static function tableName(): string {
		return '{{%key_storage_item}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['key', 'value'], 'required'],
			['key', 'unique'],
			['key', 'string', 'max' => 128],
			['comment', 'string'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(): array {
		return [
			'key' => Yii::t('common', 'Key'),
			'value' => Yii::t('common', 'Value'),
			'comment' => Yii::t('common', 'Comment'),
		];
	}
}
