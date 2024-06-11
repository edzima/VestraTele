<?php

namespace common\modules\court\models;

use common\models\Address;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "court".
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $phone
 * @property string|null $fax
 * @property string $email
 * @property string $updated_at
 * @property int $parent_id
 *
 * @property-read Court|null $parent
 * @property-read Address[] $addresses
 */
class Court extends ActiveRecord {

	public const TYPE_APPEAL = 'SA';
	public const TYPE_REGIONAL = 'SO';
	public const TYPE_DISTRICT = 'SR';

	public function __toString() {
		return $this->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%court}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['name', 'type', 'phone', 'email'], 'required'],
			[['parent_id'], 'integer'],
			[['updated_at'], 'safe'],
			[['phone'], 'string'],
			[['name', 'phone', 'fax', 'email'], 'trim'],
			[['name', 'fax', 'email'], 'string', 'max' => 255],
			[['type'], 'string', 'max' => 2],
			[['name'], 'unique'],
			[['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => static::class, 'targetAttribute' => ['parent_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('court', 'ID'),
			'name' => Yii::t('court', 'Name'),
			'type' => Yii::t('court', 'Type'),
			'typeName' => Yii::t('court', 'Type'),
			'phone' => Yii::t('court', 'Phone'),
			'fax' => Yii::t('court', 'Fax'),
			'email' => Yii::t('court', 'Email'),
			'updated_at' => Yii::t('court', 'Updated At'),
			'parent_id' => Yii::t('court', 'Parent'),
		];
	}

	public function getAddresses(): ActiveQuery {
		return $this->hasMany(Address::class, ['id' => 'address_id'])->viaTable('{{%court_address}}', ['court_id' => 'id']);
	}

	public function getParent(): ActiveQuery {
		return $this->hasOne(static::class, ['id' => 'parent_id']);
	}

	public function getChildes(): ActiveQuery {
		return $this->hasOne(static::class, ['parent_id' => 'id']);
	}

	public function getLawsuits(): ActiveQuery {
		return $this->hasMany(Lawsuit::class, ['court_id' => 'id']);
	}

	public function isAppeal(): bool {
		return $this->type === static::TYPE_APPEAL;
	}

	public function isRegional(): bool {
		return $this->type === static::TYPE_REGIONAL;
	}

	public function isDistrict(): bool {
		return $this->type === static::TYPE_DISTRICT;
	}

	public function getTypeName(): string {
		return static::getTypesNames()[$this->type];
	}

	public static function getTypesNames(): array {
		return [
			static::TYPE_APPEAL => Yii::t('court', 'Appeal'),
			static::TYPE_REGIONAL => Yii::t('court', 'Regional'),
			static::TYPE_DISTRICT => Yii::t('court', 'District'),
		];
	}

}
