<?php

namespace common\modules\lead\models\searches;

use common\models\query\PhonableQuery;
use common\modules\calendar\models\LeadReminderCalendarEvent;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadReminder;
use common\modules\lead\models\LeadStatus;
use common\modules\reminder\models\Reminder;
use common\modules\reminder\models\ReminderQuery;
use common\modules\reminder\models\searches\ReminderSearch;
use common\validators\PhoneValidator;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;
use yii\db\ActiveQuery;
use yii\db\QueryInterface;

class LeadReminderSearch extends ReminderSearch {

	public const SCENARIO_USER = 'user';
	protected const EVENT_CLASS = LeadReminderCalendarEvent::class;

	public ?string $leadName = null;
	public ?string $leadPhone = null;
	public $leadStatusId;
	public ?int $user_id = null;

	public function rules(): array {
		return array_merge([
			['!user_id', 'required', 'on' => static::SCENARIO_USER],
			['leadStatusId', 'integer'],
			['leadName', 'trim'],
			['leadName', 'string', 'min' => 3],
			['leadPhone', PhoneValidator::class],

		], parent::rules());
	}

	public function search(array $params): DataProviderInterface {
		$query = LeadReminder::find()
			->joinWith('lead');

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if (!$this->validate()) {
			$query->andWhere('0=1');
		}

		/*
		$query->joinWith([
			'lead' => function (LeadQuery $query) {
				$this->applyReminderFilter($query);
			},
		]);

		*/

		$query->joinWith([
			'reminder' => function (ReminderQuery $query) {
				$this->applyReminderFilter($query);
			},
		]);
		$this->applyLeadPhoneFilter($query);
		$this->applyLeadNameFilter($query);
		$this->applyLeadStatusFilter($query);

		if ($this->scenario === static::SCENARIO_USER) {
			if (empty($this->user_id)) {
				throw new InvalidConfigException('User Id cannot be blank on User scenario.');
			}
			$query->joinWith([
				'lead.leadUsers' => function (QueryInterface $query) {
					$query->andWhere(['user_id' => $this->user_id]);
				},
			]);
		}

		return $dataProvider;
	}

	public function attributeLabels(): array {
		return array_merge(parent::attributeLabels(), [
				'leadName' => Yii::t('lead', 'Lead Name'),
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
		$models = $this->search([])->getModels();
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

	public static function getStatusesFilters(): array {
		$models = LeadStatus::find()
			->andWhere('calendar_background IS NOT NULL')
			->all();
		$options = [];
		foreach ($models as $model) {
			$options[] = [
				'value' => $model->id,
				'isActive' => true,
				'label' => $model->name,
				'color' => $model->calendar_background,
			];
		}
		return $options;
	}

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

}
