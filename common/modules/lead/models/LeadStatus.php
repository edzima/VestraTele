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
 * @property int|null $market_status_same_contacts
 * @property string|null $calendar_background
 * @property string|null $chart_color
 * @property string|null $chart_group
 * @property string|null $statuses
 * @property int|null $hours_deadline
 * @property int|null $hours_deadline_warning
 * @property int|null $deal_stage
 * @property-read Lead[] $leads
 * @property-read string $marketStatusName
 */
class LeadStatus extends ActiveRecord implements
	LeadStatusInterface,
	LeadDealStage {

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
			[['sort_index', 'market_status', 'hours_deadline', 'hours_deadline_warning', 'deal_stage'], 'integer'],
			[['hours_deadline', 'hours_deadline_warning'], 'integer', 'min' => 0],
			[['short_report', 'show_report_in_lead_index', 'not_for_dialer', 'market_status_same_contacts'], 'boolean'],
			[['name', 'description', 'statuses', 'calendar_background', 'chart_color', 'chart_group'], 'string', 'max' => 255],
			[['calendar_background', 'hours_deadline', 'hours_deadline_warning', 'statuses', 'chart_color', 'chart_group', 'deal_stage'], 'default', 'value' => null],
			[['market_status'], 'in', 'range' => array_keys(static::getMarketStatusesNames())],
			['statusesIds', 'in', 'range' => array_keys(static::getNames()), 'allowArray' => true],
			['deal_stage', 'in', 'range' => array_keys($this->dealStagesNames()), 'enableClientValidation' => false],
		];
	}

	public function getStatusesNames(): array {
		$names = [];
		foreach ($this->getStatusesIds() as $id) {
			$name = static::getNames()[$id] ?: null;
			if ($name) {
				$names[$id] = $name;
			}
		}
		return $names;
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
			'market_status_same_contacts' => Yii::t('lead', 'Market Status same Contacts'),
			'calendar_background' => Yii::t('lead', 'Calendar Background'),
			'hours_deadline' => Yii::t('lead', 'Hours deadline'),
			'hours_deadline_warning' => Yii::t('lead', 'Hours deadline warning'),
			'statuses' => Yii::t('lead', 'Statuses'),
			'chart_color' => Yii::t('lead', 'Chart Color'),
			'chart_group' => Yii::t('lead', 'Chart Group'),
			'deal_stage' => Yii::t('lead', 'Deal Stage'),
			'dealStageName' => Yii::t('lead', 'Deal Stage'),
		];
	}

	public function getStatusesIds(): array {
		if (empty($this->statuses)) {
			return [];
		}
		return explode('|', $this->statuses);
	}

	public function setStatusesIds($ids): void {
		if (empty($ids)) {
			$this->statuses = null;
		} else {
			if (is_string($ids)) {
				$ids = [$ids];
			}
			$this->statuses = implode('|', $ids);
		}
	}

	/**
	 * Gets query for [[Leads]].
	 *
	 * @return ActiveQuery
	 */
	public function getLeads() {
		return $this->hasMany(Lead::class, ['status_id' => 'id']);
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

	public function getDealStage(): ?int {
		return $this->deal_stage;
	}

	public function getDealStageName(): ?string {
		return $this->dealStagesNames()[$this->deal_stage] ?? null;
	}

	public function dealStagesNames(): array {
		return [
			static::DEAL_STAGE_QUALIFIED => Yii::t('lead', 'Deal Stage: Qualified'),
			static::DEAL_STAGE_CONTRACT_SENT => Yii::t('lead', 'Deal Stage: Contract Sent'),
			static::DEAL_STAGE_CLOSED_WON => Yii::t('lead', 'Deal Stage: Closed Won'),
			static::DEAL_STAGE_CLOSED_LOST => Yii::t('lead', 'Deal Stage: Closed Lost'),
		];
	}

	public static function getNames(): array {
		return ArrayHelper::map(static::getModels(), 'id', 'name');
	}

	public static function sortIndexByKey($groupOrId, bool $grouped = false, int $default = -1): int {
		$model = $grouped ? static::findStatusByChartGroup($groupOrId) : static::getModels()[$groupOrId];
		return $model ? (int) $model->sort_index : $default;
	}

	public static function findStatusByChartGroup(string $name): ?LeadStatus {
		foreach (static::getModels() as $model) {
			if ($model->chart_group === $name) {
				return $model;
			}
		}
		return null;
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

}
