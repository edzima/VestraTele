<?php

namespace common\models\user;

use borales\extensions\phoneInput\PhoneInputBehavior;
use borales\extensions\phoneInput\PhoneInputValidator;
use common\models\user\query\UserProfileQuery;
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
 * @property string $tax_office
 * @property string $pesel
 * @property bool $email_hidden_in_frontend_issue
 */
class UserProfile extends ActiveRecord {

	private const GENDER_MALE = 1;
	private const GENDER_FEMALE = 2;

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
			'phoneInput' => [
				'class' => PhoneInputBehavior::class,
				'attributes' => ['phone', 'phone_2'],
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			['firstname', 'trim'],
			['firstname', 'required'],
			['lastname', 'trim'],
			['lastname', 'required'],
			[['birthday'], 'date', 'format' => 'Y-m-d'],
			['gender', 'in', 'range' => [null, self::GENDER_MALE, self::GENDER_FEMALE]],
			['website', 'trim'],
			['website', 'url', 'defaultScheme' => 'http', 'validSchemes' => ['http', 'https']],
			['other', 'string', 'max' => 1024],
			[['phone', 'phone_2'], 'string', 'max' => 20],
			[['pesel'], 'string', 'max' => 11],
			[['email_hidden_in_frontend_issue'], 'boolean'],
			[['phone', 'phone_2'], PhoneInputValidator::class],
			[['firstname', 'lastname', 'avatar_path', 'website'], 'string', 'max' => 255],
			[['firstname', 'lastname'], 'match', 'pattern' => '/[AaĄąBbCcĆćDdEeĘęFfGgHhIiJjKkLlŁłMmNnŃńOoÓóPpRrSsŚśTtUuWwYyZzŹźŻż]/iu'],
			['user_id', 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
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
			'pesel' => Yii::t('common', 'PESEL'),
			'tax_office' => Yii::t('settlement', 'Tax Office'),
			'email_hidden_in_frontend_issue' => Yii::t('issue', 'Email hidden in Frontend Issue'),
		];
	}

	public function getGenderName(): ?string {
		return static::getGendersNames()[$this->gender] ?? null;
	}

	public static function getGendersNames(): array {
		return [
			static::GENDER_MALE => Yii::t('common', 'Male'),
			static::GENDER_FEMALE => Yii::t('common', 'Female'),
		];
	}

	public static function find(): UserProfileQuery {
		return new UserProfileQuery(static::class);
	}

	public function hasPhones(): bool {
		return !empty($this->phone) || !empty($this->phone_2);
	}
}
