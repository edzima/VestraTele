<?php

namespace common\modules\calendar\models\searches;

use common\helpers\Html;
use common\models\issue\query\SummonQuery;
use common\models\issue\Summon;
use common\models\issue\SummonType;
use common\models\user\User;
use common\modules\calendar\models\SummonCalendarEvent;
use common\modules\reminder\models\Reminder;
use common\modules\reminder\models\ReminderQuery;
use frontend\models\search\SummonSearch;
use Yii;
use yii\data\ActiveDataProvider;

class ContactorSummonCalendarSearch extends SummonSearch {

	public const SCENARIO_DEADLINE = 'deadline';
	public const SCENARIO_REMINDER = 'reminder';
	public string $start;
	public string $end;
	public int $contractor_id;
	public int $owner_id;
	public int $typeId;
	public int $title;

	public array $eventConfig = [
		'class' => SummonCalendarEvent::class,
	];

	public function rules(): array {
		return [
			[['contractor_id', 'start', 'end'], 'required', 'on' => [static::SCENARIO_DEADLINE, static::SCENARIO_DEFAULT, static::SCENARIO_REMINDER]],
			[['contractor_id'], 'integer'],
		];
	}

	public function getEventsData(array $config = []): array {
		$data = [];
		foreach ($this->search()->getModels() as $model) {
			/** @var Summon $model */
			$event = $this->createEvent($config);
			$event->is = $this->getEventKind();
			if ($this->scenario === static::SCENARIO_REMINDER) {
				$reminders = $this->getSummonReminders($model);
				foreach ($reminders as $reminder) {
					$event->setReminder($reminder);
					$event->setModel($model);
					$data[] = $event->toArray();
				}
			} else {
				$event->setModel($model);
			}
			$data[] = $event->toArray();
		}
		return $data;
	}

	protected function getSummonReminders(Summon $model): array {
		return array_filter($model->reminders, function (Reminder $reminder): bool {
			$time = strtotime($reminder->date_at);
			return $time > strtotime($this->start)
				&& $time < strtotime($this->end);
		});
	}

	/**
	 * @param array $params
	 * @return ActiveDataProvider
	 */
	public function search(array $params = []): ActiveDataProvider {
		$query = Summon::find();
		$query->with('docs');
		$query->with('issue.customer.userProfile');
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => ['updated_at' => SORT_DESC],
			],
			'pagination' => false,
		]);

		if (!$this->validate()) {
			$query->andWhere('0=1');
			return $dataProvider;
		}

		$this->applyDateFilter($query);
		$this->applyExcludedStatusesFilter($query);
		$this->applyIssueMainTypeFilter($query);
		$this->applyContractorFilter($query);
		return $dataProvider;
	}

	protected function applyContractorFilter(SummonQuery $query): void {
		$query->andWhere([
			'contractor_id' => $this->contractor_id,
		]);
	}

	private function applyDateFilter(SummonQuery $query) {
		switch ($this->scenario) {
			case self::SCENARIO_DEADLINE:
				$query
					->andWhere(['>=', Summon::tableName() . '.deadline_at', $this->start])
					->andWhere(['<=', Summon::tableName() . '.deadline_at', $this->end]);
				break;
			case self::SCENARIO_REMINDER:
				$this->applyReminderFilter($query);
				break;
			default:
				$query
					->andWhere(['>=', Summon::tableName() . '.realize_at', $this->start])
					->andWhere(['<=', Summon::tableName() . '.realize_at', $this->end]);
				break;
		}
	}

	protected function applyExcludedStatusesFilter(SummonQuery $query): void {
		if ($this->scenario !== static::SCENARIO_REMINDER) {
			$query->andFilterWhere([
				'NOT IN', Summon::tableName() . '.status', static::getExcludedStatuses(),
			]);
		}
	}

	private function applyReminderFilter(SummonQuery $query): void {
		if ($this->scenario === static::SCENARIO_REMINDER) {
			$query->joinWith([
				'reminders' => function (ReminderQuery $query) {
					if (!empty($this->contractor_id)) {
						$query->onlyUser($this->contractor_id);
					}
					$query
						->andWhere(['>=', Reminder::tableName() . '.date_at', $this->start])
						->andWhere(['<=', Reminder::tableName() . '.date_at', $this->end]);
				},
			]);
		}
	}

	public function createEvent(array $config = []): SummonCalendarEvent {
		$config = array_merge($config, $this->eventConfig);
		if (!isset($config['class'])) {
			$config['class'] = SummonCalendarEvent::class;
		}
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return Yii::createObject($config);
	}

	protected function getEventKind(): string {
		switch ($this->scenario) {
			case static::SCENARIO_DEADLINE:
				return SummonCalendarEvent::IS_DEADLINE;
			case static::SCENARIO_REMINDER:
				return SummonCalendarEvent::IS_REMINDER;
			default:
				return SummonCalendarEvent::IS_SUMMON;
		}
	}

	public static function getStatusesNames(): array {
		$statuses = parent::getStatusesNames();
		foreach (static::getExcludedStatuses() as $statusId) {
			unset($statuses[$statusId]);
		}
		return $statuses;
	}

	public function getStatusFiltersOptions(): array {
		$options = [];
		$query = Summon::find()
			->select('status')
			->distinct();
		$this->applyIssueMainTypeFilter($query);
		$this->applyContractorFilter($query);
		$ids = $query->column();
		$statusNames = static::getStatusesNames();
		$event = $this->createEvent();
		foreach ($statusNames as $statusId => $statusName) {
			if (in_array($statusId, $ids)) {
				$options[] = [
					'value' => $statusId,
					'isActive' => true,
					'label' => $statusName,
					'color' => $event::getStatusesBackgroundColors()[$statusId],
				];
			}
		}
		return $options;
	}

	public function getTypesFilterOptions(): array {
		$options = [];
		$query = Summon::find()
			->select(Summon::tableName() . '.type_id')
			->joinWith('type')
			->andWhere('calendar_background IS NOT NULL')
			->orderBy(SummonType::tableName() . '.name')
			->distinct();
		$this->applyIssueMainTypeFilter($query);
		$this->applyContractorFilter($query);
		$types = SummonType::find()->andWhere(['id' => $query])->all();
		foreach ($types as $type) {
			/**
			 * @var SummonType $type
			 */
			$color = $type->calendar_background;
			$options[] = [
				'value' => $type->id,
				'isActive' => true,
				'label' => Html::encode($type->name),
				'color' => $color,
				'badge' => [
					'background' => $color,
					'text' => Html::encode($type->short_name),
				],
			];
		}
		return $options;
	}

	public static function getExcludedStatuses(): array {
		return Summon::notActiveStatuses();
	}

	public function getKindFilterOptions(): array {
		$event = $this->createEvent();
		$options = [];
		$options[] = [
			'value' => SummonCalendarEvent::IS_SUMMON,
			'isActive' => true,
			'label' => Yii::t('issue', 'Summons'),
			'color' => $event->borderColors[SummonCalendarEvent::IS_SUMMON] ?? '#2196F3',
		];
		$options[] = [
			'value' => SummonCalendarEvent::IS_DEADLINE,
			'isActive' => true,
			'label' => Yii::t('issue', 'Deadline'),
			'color' => $event->borderColors[SummonCalendarEvent::IS_DEADLINE] ?? 'red',
		];
		$options[] = [
			'value' => SummonCalendarEvent::IS_REMINDER,
			'isActive' => true,
			'label' => Yii::t('issue', 'Reminder'),
			'color' => $event->borderColors[SummonCalendarEvent::IS_REMINDER] ?? 'yellow',
		];
		return $options;
	}

	public static function getSelfContractorsNames(int $userId): array {
		$ids = Summon::find()
			->select('contractor_id')
			->distinct()
			->andWhere(['owner_id' => $userId])
			->column();

		$ids[] = $userId;
		return User::getSelectList($ids, false);
	}

}
