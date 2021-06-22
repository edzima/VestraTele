<?php

namespace common\modules\lead\models;

use common\modules\lead\Module;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "lead_source".
 *
 * @property int $id
 * @property string $name
 * @property int $type_id
 * @property string|null $phone
 * @property string|null $url
 * @property int|null $sort_index
 * @property int|null $owner_id
 *
 * @property-read Lead[] $leads
 * @property-read LeadType $leadType
 */
class LeadSource extends ActiveRecord implements LeadSourceInterface {

	public const SCENARIO_OWNER = 'owner';

	private static ?array $models = null;

	public function __toString(): string {
		return $this->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%lead_source}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['name', 'type_id'], 'required'],
			['!owner_id', 'required', 'on' => static::SCENARIO_OWNER],
			[['sort_index'], 'integer'],
			[['name', 'url'], 'string', 'max' => 255],
			['phone', 'string', 'max' => 30],
			[['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeadType::class, 'targetAttribute' => ['type_id' => 'id']],
			[['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Module::userClass(), 'targetAttribute' => ['owner_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('lead', 'ID'),
			'name' => Yii::t('lead', 'Name'),
			'url' => Yii::t('lead', 'URL'),
			'phone' => Yii::t('lead', 'Phone'),
			'sort_index' => Yii::t('lead', 'Sort Index'),
			'type_id' => Yii::t('lead', 'Type'),
		];
	}

	public function getLeads(): ActiveQuery {
		return $this->hasMany(Lead::class, ['source_id' => 'id']);
	}

	public function getLeadType(): ActiveQuery {
		return $this->hasOne(LeadType::class, ['id' => 'type_id']);
	}

	/**
	 * Gets query for [[User]].
	 *
	 * @return ActiveQuery
	 */
	public function getOwner(): ActiveQuery {
		return $this->hasOne(Module::userClass(), ['id' => 'owner_id']);
	}

	public static function getNames(): array {
		return ArrayHelper::map(static::getModels(), 'id', 'name');
	}

	public static function getModels(bool $refresh = false): array {
		if (static::$models === null || $refresh) {
			static::$models = static::find()
				->indexBy('id')
				->orderBy('sort_index')
				->all();
		}
		return static::$models;
	}

	public function getID(): string {
		return $this->id;
	}

	public function getName(): string {
		return $this->name;
	}

	public function getURL(): ?string {
		return $this->url;
	}

	public function getOwnerId(): ?int {
		return $this->owner_id;
	}

	public function getType(): LeadTypeInterface {
		return LeadType::getModels()[$this->type_id];
	}

	public function getPhone(): ?string {
		return $this->phone;
	}
}
