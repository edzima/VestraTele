<?php

namespace common\modules\lead\models;

use common\modules\lead\models\query\LeadQuery;
use common\modules\lead\Module;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "lead_campaign".
 *
 * @property int $id
 * @property string $name
 * @property int|null $owner_id
 * @property int|null $parent_id
 * @property int|null $sort_index
 * @property int $is_active
 * @property string|null $entity_id
 * @property string|null $details
 * @property string|null $type
 *
 * @property-read Lead[] $leads
 * @property-read LeadCampaign $parent
 * @property-read LeadUserInterface $owner
 */
class LeadCampaign extends ActiveRecord {

	public const SCENARIO_OWNER = 'owner';

	public const TYPE_CAMPAIGN = 'campaign';
	public const TYPE_ADSET = 'adset';
	public const TYPE_AD = 'ad';

	private static ?array $models = null;

	public function __toString(): string {
		return $this->name;
	}

	public function getNameWithOwner(): string {
		if ($this->owner_id && $this->owner) {
			return $this->name . ' - ' . $this->owner ?? Yii::t('common', 'Deleted');
		}
		return $this->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%lead_campaign}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['name'], 'required'],
			[['sort_index', 'parent_id'], 'integer'],
			[['name'], 'string', 'max' => 255],
			[['is_active'], 'boolean'],
			[['name', 'entity_id', 'type', 'details'], 'string', 'max' => 255],
			['!owner_id', 'required', 'on' => static::SCENARIO_OWNER],
			[['name', 'entity_id'], 'unique', 'targetAttribute' => ['name', 'entity_id']],
			[['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => static::class, 'targetAttribute' => ['parent_id' => 'id']],
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
			'parent_id' => Yii::t('lead', 'Parent'),
			'owner_id' => Yii::t('lead', 'Owner'),
			'sort_index' => Yii::t('lead', 'Sort Index'),
			'type' => Yii::t('lead', 'Type'),
			'details' => Yii::t('lead', 'Details'),
			'entity_id' => Yii::t('lead', 'Entity ID'),
			'is_active' => Yii::t('lead', 'Is Active'),
			'typeName' => Yii::t('lead', 'Type'),
			'parent' => Yii::t('lead', 'Parent Campaign'),
		];
	}

	/**
	 * Gets query for [[Leads]].
	 *
	 * @return ActiveQuery
	 */
	public function getLeads(): LeadQuery {
		return $this->hasMany(Lead::class, ['campaign_id' => 'id']);
	}

	public function getParent(): ActiveQuery {
		return $this->hasOne(static::class, ['id' => 'parent_id']);
	}

	public function getOwner(): ActiveQuery {
		return $this->hasOne(Module::userClass(), ['id' => 'owner_id']);
	}

	public function getTypeName(): ?string {
		return static::getTypesNames()[$this->type] ?? null;
	}

	public static function getNames(int $owner_id = null, bool $active = true): array {
		$models = static::getModels();
		if ($owner_id) {
			$models = array_filter($models, static function (LeadCampaign $model) use ($owner_id): bool {
				return $model->owner_id === null || $model->owner_id === $owner_id;
			});
		}
		if ($active) {
			$models = array_filter($models, static function (LeadCampaign $model): bool {
				return $model->is_active;
			});
		}
		return ArrayHelper::map($models, 'id', $owner_id ? 'name' : 'nameWithOwner');
	}

	/**
	 * @param bool $refresh
	 * @return static[]
	 */
	public static function getModels(bool $refresh = false): array {
		if (static::$models === null || $refresh) {
			static::$models = static::find()
				->indexBy('id')
				->joinWith('owner.userProfile')
				->orderBy('sort_index')
				->all();
		}
		return static::$models;
	}

	public static function getTypesNames(): array {
		return [
			static::TYPE_CAMPAIGN => Yii::t('lead', 'Campaign'),
			static::TYPE_ADSET => Yii::t('lead', 'Adset'),
			static::TYPE_AD => Yii::t('lead', 'Ad'),
		];
	}

	public function getFullName(): string {
		$names = [];
		$model = $this;
		$names[] = $model->name;
		while ($model->parent) {
			$model = $model->parent;
			$names[] = $model->name;
		}
		$names = array_reverse($names);
		return implode(' ', $names);
	}

	public function getCosts() {
		return $this->hasMany(LeadCost::class, [
			'campaign_id' => 'id',
		]);
	}
}
