<?php

namespace common\modules\calendar\models\searches;

use common\helpers\Html;
use common\models\issue\query\SummonQuery;
use common\models\issue\Summon;
use common\models\issue\SummonType;
use common\models\user\User;
use common\modules\calendar\models\SummonCalendarEvent;
use frontend\models\search\SummonSearch;
use Yii;
use yii\data\ActiveDataProvider;

class ContactorSummonCalendarSearch extends SummonSearch {

	public const SCENARIO_DEADLINE = 'deadline';
	public string $start;
	public string $end;
	public int $contractor_id;
	public int $owner_id;
	public int $typeId;
	public int $title;

	protected const EVENT_CLASS = SummonCalendarEvent::class;

	public static function getSelfContractorsNames(int $userId): array {
		$ids = Summon::find()
			->select('contractor_id')
			->distinct()
			->andWhere(['owner_id' => $userId])
			->column();

		$ids[] = $userId;
		return User::getSelectList($ids, false);
	}

	public function rules(): array {
		return [
			[['contractor_id', 'start', 'end'], 'required', 'on' => [static::SCENARIO_DEADLINE, static::SCENARIO_DEFAULT]],
			[['contractor_id'], 'integer'],
		];
	}

	public function getEventsData(array $config = []): array {
		$data = [];
		foreach ($this->search()->getModels() as $model) {
			$event = static::createEvent($config);
			if ($this->scenario === static::SCENARIO_DEADLINE) {
				$event->is = SummonCalendarEvent::IS_DEADLINE;
			}
			$event->setModel($model);

			$data[] = $event->toArray();
		}
		return $data;
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
		$query->andFilterWhere([
			'NOT IN', Summon::tableName() . '.status', static::getExcludedStatuses(),
		]);
		$this->applyDateFilter($query);
		$query->andWhere([
			'contractor_id' => $this->contractor_id,
		]);
		return $dataProvider;
	}

	public static function getExcludedStatuses(): array {
		return Summon::notActiveStatuses();
	}

	public static function getStatusFiltersOptions(): array {
		$options = [];
		$statusNames = static::getStatusesNames();
		$event = static::createEvent();
		foreach ($statusNames as $statusId => $statusName) {
			$options[] = [
				'value' => $statusId,
				'isActive' => true,
				'label' => $statusName,
				'color' => $event::getStatusesBackgroundColors()[$statusId],
			];
		}
		return $options;
	}

	public static function createEvent(array $config = []): SummonCalendarEvent {
		if (!isset($config['class'])) {
			$config['class'] = static::EVENT_CLASS;
		}
		return Yii::createObject($config);
	}

	public static function getStatusesNames(): array {
		$statuses = parent::getStatusesNames();
		foreach (static::getExcludedStatuses() as $statusId) {
			unset($statuses[$statusId]);
		}
		return $statuses;
	}

	public static function getTypesFilterOptions(): array {
		$options = [];
		$types = SummonType::find()
			->andWhere('calendar_background IS NOT NULL')
			->orderBy('name')
			->all();

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

	public static function getKindFilterOptions(): array {
		$options = [];
		$options[] = [
			'value' => SummonCalendarEvent::IS_SUMMON,
			'isActive' => true,
			'label' => Yii::t('issue', 'Summons'),
			'color' => '#2196F3',
		];
		$options[] = [
			'value' => SummonCalendarEvent::IS_DEADLINE,
			'isActive' => true,
			'label' => Yii::t('issue', 'Deadline'),
			'color' => SummonCalendarEvent::DEADLINE_BACKGROUND_COLOR,
		];
		return $options;
	}

	private function applyDateFilter(SummonQuery $query) {
		if ($this->scenario === static::SCENARIO_DEADLINE) {
			$query
				->andWhere(['>=', Summon::tableName() . '.deadline_at', $this->start])
				->andWhere(['<=', Summon::tableName() . '.deadline_at', $this->end]);
			return;
		}
		$query
			->andWhere(['>=', Summon::tableName() . '.realize_at', $this->start])
			->andWhere(['<=', Summon::tableName() . '.realize_at', $this->end]);
	}
}
