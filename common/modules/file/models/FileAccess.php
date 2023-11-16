<?php

namespace common\modules\file\models;

use common\models\user\User;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "file_access".
 *
 * @property int $file_id
 * @property int $user_id
 * @property string $access
 *
 * @property File $file
 * @property User $user
 */
class FileAccess extends ActiveRecord {

	private static array $usersAccessMap = [];

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%file_access}}';
	}

	public static function userHasAccess(int $userId, int $id) {
		return isset(static::userFileMap($userId)[$id]);
	}

	private static function userFileMap(int $userId): array {
		if (!isset(static::$usersAccessMap[$userId])) {
			static::$usersAccessMap[$userId] = static::find()
				->select('file_id')
				->andWhere(['user_id' => $userId])
				->indexBy('file_id')
				->column();
		}
		return static::$usersAccessMap[$userId];
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['file_id', 'user_id', 'access'], 'required'],
			[['file_id', 'user_id'], 'integer'],
			[['access'], 'string', 'max' => 255],
			[['file_id', 'user_id'], 'unique', 'targetAttribute' => ['file_id', 'user_id']],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => File::class, 'targetAttribute' => ['user_id' => 'id']],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'file_id' => Yii::t('file', 'File ID'),
			'user_id' => Yii::t('file', 'User ID'),
			'access' => Yii::t('file', 'Access'),
			'user' => Yii::t('file', 'User'),
		];
	}

	/**
	 * Gets query for [[File]].
	 *
	 * @return ActiveQuery
	 */
	public function getFile() {
		return $this->hasOne(File::class, ['id' => 'file_id']);
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
