<?php

namespace common\modules\lead\models;

use common\modules\calendar\models\Filter;
use common\modules\calendar\models\FilterOptions;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * This is the model class for table "lead_status".
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int|null $sort_index
 * @property int|null $short_report
 * @property int|null $show_report_in_lead_index
 * @property string $calendar
 *
 * @property Lead[] $leads
 */
class LeadStatus extends ActiveRecord implements LeadStatusInterface {

	private static ?array $models = null;

	private ?FilterOptions $filterOptions = null;

	public function __toString(): string {
		return $this->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%lead_status}}';
	}

	public function load($data, $formName = null): bool {
		return parent::load($data, $formName) && $this->getFilterOptions()->load($data, $formName);
	}

	public function beforeSave($insert): bool {
		if ($this->getFilterOptions()->validate()) {
			$this->calendar = Json::encode($this->getFilterOptions()->toArray());
		}
		return parent::beforeSave($insert);
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['name'], 'required'],
			[['sort_index'], 'integer'],
			[['short_report', 'show_report_in_lead_index'], 'boolean'],
			['short_report', 'boolean'],
			['name', 'unique'],
			[['name', 'description'], 'string', 'max' => 255],
			['calendar', 'string'],
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

	public function getId(): int {
		return $this->id;
	}

	public function getName(): string {
		return $this->name;
	}

	public function isShortReport(): bool {
		return !empty($this->short_report);
	}

	public function getFilter(): Filter {
		return new Filter([
			'id' => $this->getId(),
			'label' => $this->getName(),
			'options' => $this->getFilterOptions(),
		]);
	}

	public function getFilterOptions(): FilterOptions {
		if ($this->filterOptions === null) {
			$this->filterOptions = new FilterOptions($this->getCalendarData());
		}
		return $this->filterOptions;
	}

	public function getCalendarData(): array {
		return $this->calendar ? Json::decode($this->calendar) : [];
	}
}
