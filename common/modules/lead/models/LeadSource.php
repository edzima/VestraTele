<?php

namespace common\modules\lead\models;

use borales\extensions\phoneInput\PhoneInputBehavior;
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
 * @property string|null $dialer_phone
 * @property string|null $url
 * @property string|null $sms_push_template
 * @property int|null $sort_index
 * @property int|null $owner_id
 * @property int|null $is_active
 * @property int|null $call_page_widget_id
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

	public function behaviors(): array {
		return [
			[
				'class' => PhoneInputBehavior::class,
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['name', 'type_id'], 'required'],
			['!owner_id', 'required', 'on' => static::SCENARIO_OWNER],
			[['sort_index', 'call_page_widget_id'], 'integer'],
			[['is_active'], 'boolean'],
			[['name', 'url'], 'string', 'max' => 255],
			[['phone', 'dialer_phone'], 'string', 'max' => 30],
			['sms_push_template', 'string'],
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
			'dialer_phone' => Yii::t('lead', 'Dialer Phone'),
			'sort_index' => Yii::t('lead', 'Sort Index'),
			'owner' => Yii::t('lead', 'Owner'),
			'type_id' => Yii::t('lead', 'Type'),
			'is_active' => Yii::t('lead', 'Is Active'),
			'sms_push_template' => Yii::t('lead', 'SMS Push Template'),
			'call_page_widget_id' => Yii::t('lead', 'CallPage Widget ID'),
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

	public static function getNames(
		int $owner_id = null,
		bool $withType = false,
		int $typeId = null,
		bool $active = false
	): array {
		$models = static::getModels($active);
		if ($owner_id) {
			$models = array_filter($models, static function (LeadSource $model) use ($owner_id): bool {
				return $model->owner_id === null || $model->owner_id === $owner_id;
			});
		}
		if ($typeId) {
			$models = array_filter($models, static function (LeadSource $model) use ($typeId): bool {
				return $model->type_id === $typeId;
			});
		}
		$name = $owner_id ? 'name' : 'nameWithOwner';
		if ($withType && $typeId === null) {
			$name .= 'withType';
		}
		return ArrayHelper::map($models, 'id', $name);
	}

	/**
	 * @param bool $refresh
	 * @return static[]
	 */
	public static function getModels(bool $active = false, bool $refresh = false): array {
		if (static::$models === null || $refresh) {
			static::$models = static::find()
				->indexBy('id')
				->joinWith('owner.userProfile')
				->orderBy('sort_index')
				->all();
			static::sortModels(static::$models);
		}
		if ($active) {
			return array_filter(static::$models, function (LeadSourceInterface $source): bool {
				return $source->getIsActive();
			});
		}
		return static::$models;
	}

	public static function sortModels(array &$models): void {
		uasort($models, function (LeadSource $a, LeadSource $b) {
			if ($a->sort_index || $b->sort_index) {
				$sort = $b->sort_index <=> $a->sort_index;
				if ($sort === 0) {
					return strcmp($a->name, $b->name);
				}
				return $sort;
			}
			return strcmp($a->name, $b->name);
		});
	}

	public function getID(): string {
		return $this->id;
	}

	public function getNameWithType(): string {
		return $this->getName() . ' [' . $this->getType()->getName() . ']';
	}

	public function getName(): string {
		return $this->name;
	}

	public function getNameWithOwnerWithType(): string {
		return $this->getNameWithOwner() . ' [' . $this->getType()->getName() . ']';
	}

	public function getNameWithOwner(): string {
		if ($this->owner_id && $this->owner) {
			return $this->getName() . '  (' . $this->owner . ')';
		}
		return $this->getName();
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

	public function getSmsPushTemplate(): ?string {
		return $this->sms_push_template;
	}

	public function getIsActive(): bool {
		return $this->is_active;
	}

	public function getDialerPhone(): ?string {
		return $this->dialer_phone;
	}

	public function getCallPageWidgetId(): ?int {
		return $this->call_page_widget_id;
	}

	public static function typeId(int $sourceId): int {
		return static::getModels()[$sourceId]->type_id;
	}
}
