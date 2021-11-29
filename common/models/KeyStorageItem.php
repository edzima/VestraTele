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

	public const KEY_ROBOT_SMS_OWNER_ID = 'robot-sms-owner-id';

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
