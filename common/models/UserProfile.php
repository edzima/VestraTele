<?php

namespace common\models;

use vova07\fileapi\behaviors\UploadBehavior;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%user_profile}}".
 *
 * @property integer $user_id
 * @property string $firstname
 * @property string $lastname
 * @property integer $birthday
 * @property string $avatar_path
 * @property integer $gender
 * @property string $website
 * @property string $other
 * @property string $phone
 * @property string $phone_2
 */
class UserProfile extends ActiveRecord {

	private const GENDER_MALE = 1;
	private const GENDER_FEMALE = 2;

	public static function getGendersNames(): array {
		return [
			static::GENDER_MALE => Yii::t('common', 'Male'),
			static::GENDER_FEMALE => Yii::t('common', 'Female'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName(): string {
		return '{{%user_profile}}';
	}

	/**
	 * @inheritdoc
	 */
	public function behaviors(): array {
		return [
			'uploadBehavior' => [
				'class' => UploadBehavior::class,
				'attributes' => [
					'avatar_path' => [
						'path' => '@storage/avatars',
						'tempPath' => '@storage/tmp',
						'url' => Yii::getAlias('@storageUrl/avatars'),
					],
				],
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			['birthday', 'filter', 'filter' => 'strtotime', 'skipOnEmpty' => true],
			['gender', 'in', 'range' => [null, self::GENDER_MALE, self::GENDER_FEMALE]],
			['website', 'trim'],
			['website', 'url', 'defaultScheme' => 'http', 'validSchemes' => ['http', 'https']],
			['other', 'string', 'max' => 1024],
			['phone', 'string', 'max' => 15],
			[['firstname', 'lastname', 'avatar_path', 'website'], 'string', 'max' => 255],
			[['firstname', 'lastname'], 'match', 'pattern' => '/[AaĄąBbCcĆćDdEeĘęFfGgHhIiJjKkLlŁłMmNnŃńOoÓóPpRrSsŚśTtUuWwYyZzŹźŻż]/iu'],
			['user_id', 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
			[['firstname', 'lastname', 'birthday', 'gender', 'website', 'other'], 'default', 'value' => null],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(): array {
		return [
			'firstname' => Yii::t('common', 'Firstname'),
			'lastname' => Yii::t('common', 'Lastname'),
			'birthday' => Yii::t('common', 'Birthday'),
			'avatar_path' => Yii::t('common', 'Avatar'),
			'gender' => Yii::t('common', 'Gender'),
			'website' => Yii::t('common', 'Website'),
			'other' => Yii::t('common', 'Other'),
			'phone' => Yii::t('common', 'Phone number'),
			'phone_2' => Yii::t('common', 'Phone number 2'),
		];
	}
}
