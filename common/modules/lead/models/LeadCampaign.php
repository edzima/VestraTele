<?php

namespace common\modules\lead\models;

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
 * @property string $url_search_part
 *
 * @property-read Lead[] $leads
 * @property-read LeadCampaign $parent
 */
class LeadCampaign extends ActiveRecord {

	public const SCENARIO_OWNER = 'owner';

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
			[['sort_index'], 'integer'],
			[['name', 'url_search_part'], 'string', 'max' => 255],
			['url_search_part', 'default', 'value' => null],
			['!owner_id', 'required', 'on' => static::SCENARIO_OWNER],
			[['name', 'owner_id'], 'unique', 'targetAttribute' => ['name', 'owner_id']],
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
			'url_search_part' => Yii::t('lead', 'URL Search Part'),
		];
	}

	/**
	 * Gets query for [[Leads]].
	 *
	 * @return ActiveQuery
	 */
	public function getLeads() {
		return $this->hasMany(Lead::class, ['campaign_id' => 'id']);
	}

	public function getParent(): ActiveQuery {
		return $this->hasOne(static::class, ['parent_id' => 'id']);
	}

	public function getOwner(): ActiveQuery {
		return $this->hasOne(Module::userClass(), ['id' => 'owner_id']);
	}

	public static function getNames(int $owner_id = null): array {
		$models = static::getModels();
		if ($owner_id) {
			$models = array_filter($models, static function (LeadCampaign $model) use ($owner_id): bool {
				return $model->owner_id === null || $model->owner_id === $owner_id;
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

	public static function findByURL(string $url): ?self {
		$models = static::find()
			->andWhere('url_search_part IS NOT NULL')
			->all();

		foreach ($models as $model) {
			if ($model->isForURL($url)) {
				return $model;
			}
		}

		return null;
	}

	public function isForURL(string $url): bool {
		if (empty($this->url_search_part)) {
			return false;
		}

		return strpos($url, $this->url_search_part) !== false;
	}
}
