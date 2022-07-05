<?php

namespace common\modules\lead\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "lead_status".
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int|null $sort_index
 * @property int|null $short_report
 * @property int|null $show_report_in_lead_index
 * @property int|null $not_for_dialer
 * @property int|null $market_status
 *
 * @property-read Lead[] $leads
 * @property-read string $marketStatusName
 */
class LeadStatus extends ActiveRecord implements LeadStatusInterface {

	private static ?array $models = null;

	public function __toString(): string {
		return $this->name;
	}

	public function getMarketStatusName(): ?string {
		return static::getMarketStatusesNames()[$this->market_status];
	}

	public static function getMarketStatusesNames(): array {
		return LeadMarket::getStatusesNames();
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%lead_status}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['name'], 'required'],
			[['sort_index', 'market_status'], 'integer'],
			[['short_report', 'show_report_in_lead_index', 'not_for_dialer'], 'boolean'],
			[['name', 'description'], 'string', 'max' => 255],
			[['market_status'], 'in', 'range' => array_keys(static::getMarketStatusesNames())],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('lead', 'ID'),
			'name' => Yii::t('lead', 'Name'),
			'description' => Yii::t('lead', 'Description'),
			'sort_index' => Yii::t('lead', 'Sort Index'),
			'short_report' => Yii::t('lead', 'Short Report'),
			'show_report_in_lead_index' => Yii::t('lead', 'Show Report In Lead Index'),
			'not_for_dialer' => Yii::t('lead', 'Not for Dialer'),
			'market_status' => Yii::t('lead', 'Market Status'),
			'marketStatusName' => Yii::t('lead', 'Market Status'),
		];
	}

	/**
	 * Gets query for [[Leads]].
	 *
	 * @return ActiveQuery
	 */
	public function getLeads() {
		return $this->hasMany(Lead::class, ['status_id' => 'id']);
	}

	public static function getNames(): array {
		return ArrayHelper::map(static::getModels(), 'id', 'name');
	}

	/**
	 * @param bool $refresh
	 * @return static[]
	 */
	public static function getModels(bool $refresh = false): array {
		if (empty(static::$models) || $refresh) {
			static::$models = static::find()
				->indexBy('id')
				->orderBy(['sort_index' => SORT_DESC])
				->all();
		}
		return static::$models;
	}

	public static function notForDialer(int $id): bool {
		return (bool) static::getModels()[$id]->not_for_dialer;
	}

	public function getId(): int {
		return $this->id;
	}

	public function getName(): string {
		return $this->name;
	}

	public function isShortReport(): bool {
		return !empty($this->short_report);
	}
}
