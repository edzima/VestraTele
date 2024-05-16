<?php

namespace common\modules\lead\models\searches;

use common\models\query\PhonableQuery;
use common\models\user\User;
use common\modules\calendar\models\LeadReminderCalendarEvent;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadReminder;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\query\LeadQuery;
use common\modules\reminder\models\Reminder;
use common\modules\reminder\models\ReminderQuery;
use common\modules\reminder\models\searches\ReminderSearch;
use common\validators\PhoneValidator;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class LeadReminderSearch extends ReminderSearch {

	public const SCENARIO_USER = 'user';
	protected const EVENT_CLASS = LeadReminderCalendarEvent::class;

	public ?string $leadName = null;
	public ?string $leadPhone = null;
	public $leadStatusId;
	public ?int $leadUserId = null;

	public $leadReminderUserId;
	public $leadDateAt;

	public $hideFromMarket = true;

	public function rules(): array {
		return array_merge([
			['!leadUserId', 'required', 'on' => static::SCENARIO_USER],
			[['leadStatusId', 'user_id', 'leadUserId'], 'integer'],
			[['hideDone'], 'boolean'],
			[['hideDone'], 'default', 'value' => null],
			['leadName', 'trim'],
			['leadName', 'string', 'min' => 3],
			['leadPhone', PhoneValidator::class],

		], parent::rules());
	}

	public function search(array $params): ActiveDataProvider {
		$query = LeadReminder::find()
			->joinWith('lead');

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => [
					'date_at' => SORT_ASC,
					'priority' => SORT_DESC,
				],
				'attributes' => [
					'leadName' => [
						'asc' => [Lead::tableName() . '.name' => SORT_ASC],
						'desc' => [Lead::tableName() . '.name' => SORT_DESC],
					],
					'priority',
					'created_at',
					'updated_at',
					'done_at',
					'user_id',
					'date_at' => [
						'asc' => [Reminder::tableName() . '.date_at' => SORT_ASC],
						'desc' => [Reminder::tableName() . '.date_at' => SORT_DESC],
					],
					'leadDateAt' => [
						'asc' => [Lead::tableName() . '.date_at' => SORT_ASC],
						'desc' => [Lead::tableName() . '.date_at' => SORT_DESC],
					],
				],
			],
		]);

		$this->load($params);

		if (!$this->validate()) {
			$query->andWhere('0=1');
			Yii::warning($this->getErrors(), __METHOD__);
			return $dataProvider;
		}

		$query->joinWith([
			'reminder' => function (ReminderQuery $query) {
				$this->applyReminderFilter($query);
			},
		]);
		$this->applyLeadMarketFilter($query);
		$this->applyLeadPhoneFilter($query);
		$this->applyLeadNameFilter($query);
		$this->applyLeadStatusFilter($query);

		if ($this->scenario === static::SCENARIO_USER) {
			if (empty($this->leadUserId)) {
				throw new InvalidConfigException('User Id cannot be blank on User scenario.');
			}
			$query->joinWith([
				'lead' => function (LeadQuery $query) {
					$query->user($this->leadUserId);
				},
			]);
		}

		return $dataProvider;
	}

	public function attributeLabels(): array {
		return array_merge(parent::attributeLabels(), [
				'leadName' => Yii::t('lead', 'Lead Name'),
				'hideDone' => Yii::t('lead', 'Hide Done'),
				'done_at' => Yii::t('lead', 'Done At'),
			]
		);
	}

	private function applyLeadNameFilter(ActiveQuery $query) {
		if (!empty($this->leadName)) {
			$query->andFilterWhere(['like', Lead::tableName() . '.name', $this->leadName]);
		}
	}

	private function applyLeadStatusFilter(ActiveQuery $query) {
		if (!empty($this->leadStatusId)) {
			$query->andFilterWhere([Lead::tableName() . '.status_id' => $this->leadStatusId]);
		}
	}

	public function getEventsData(): array {
		$dataProvider = $this->search([]);
		$dataProvider->pagination = false;
		$models = $dataProvider->getModels();
		$data = [];
		foreach ($models as $model) {
			$event = static::createEvent();
			$event->setModel($model);
			$data[] = $event->toArray();
		}
		return $data;
	}

	public static function getPriorityFilters(): array {
		$options = [];
		$priorityNames = static::getPriorityNames();
		$event = static::createEvent();
		foreach ($priorityNames as $priority => $name) {
			$color = $event::getPriorityColors()[$priority];
			$badgeText = '!';
			switch ($priority) {
				case Reminder::PRIORITY_LOW:
					$badgeText = ' ! ';
					break;
				case Reminder::PRIORITY_MEDIUM:
					$badgeText = ' ! ! ';
					break;
				case Reminder::PRIORITY_HIGH:
					$badgeText = ' ! ! ! ';
			}
			$options[] = [
				'value' => $priority,
				'isActive' => true,
				'label' => $name,
				'color' => $event::getPriorityColors()[$priority],
				'badge' => [
					'text' => $badgeText,
					'background' => $color,
					'color' => 'red',
				],
			];
		}
		return $options;
	}

	public static function getIsDoneFilters(): array {
		return [
			[
				'label' => Yii::t('lead', 'Yes'),
				'value' => true,
				'isActive' => false,
				'color' => '#67ca67',

			],
			[
				'label' => Yii::t('lead', 'No'),
				'value' => false,
				'isActive' => true,
				'color' => '#443e3e',

			],
		];
	}

	public static function getStatusDeadlineFilters(): array {
		return [
			[
				'label' => Yii::t('lead', 'Deadline'),
				'value' => true,
				'isActive' => true,
				'color' => '#67ca67',
			],
		];
	}

	public static function getStatusesFilters(): array {
		$models = LeadStatus::find()
			->andWhere('calendar_background IS NOT NULL')
			->all();
		$options = [];
		foreach ($models as $model) {
			/** @var LeadStatus $model */
			$options[] = [
				'value' => $model->id,
				'isActive' => true,
				'label' => $model->name,
				'color' => $model->calendar_background,
			];
		}
		return $options;
	}

	/** @noinspection PhpIncompatibleReturnTypeInspection */
	protected static function createEvent(array $config = []): LeadReminderCalendarEvent {
		if (!isset($config['class'])) {
			$config['class'] = static::EVENT_CLASS;
		}
		return Yii::createObject($config);
	}

	private function applyLeadPhoneFilter(ActiveQuery $query): void {
		if (!empty($this->leadPhone)) {
			$query->joinWith([
				'lead' => function (PhonableQuery $query) {
					$query->withPhoneNumber($this->leadPhone);
				},
			]);
		}
	}

	public function getUsersNames(): array {
		$names[static::REMINDER_USER_AS_NULL] = Yii::t('lead', 'Without User Reminder');
		return $names +
			User::getSelectList(LeadReminder::find()
				->select('user_id')
				->joinWith('reminder')
				->distinct()
				->andWhere('user_id IS NOT NULL')
				->column(),
				false);
	}

	private function applyLeadMarketFilter(ActiveQuery $query): void {
		if ($this->hideFromMarket) {
			$query->joinWith('lead.market', false, 'LEFT OUTER JOIN');
			$query->andWhere(LeadMarket::tableName() . '.lead_id IS NULL');
			$query->groupBy(LeadReminder::tableName() . '.reminder_id');
		}
	}

}
