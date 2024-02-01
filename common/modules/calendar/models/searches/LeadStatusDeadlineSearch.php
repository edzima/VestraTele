<?php

namespace common\modules\calendar\models\searches;

use common\modules\calendar\models\LeadStatusDeadlineEvent;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\query\LeadQuery;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\db\ActiveQuery;

class LeadStatusDeadlineSearch extends Model {

	public $startAt;
	public $endAt;

	public $leadUserId;

	public const SCENARIO_USER = 'user';

	public $event = [
		'class' => LeadStatusDeadlineEvent::class,
	];

	public function rules(): array {
		return [
			[['startAt', 'endAt'], 'required'],
			[['!leadUserId'], 'required', 'on' => static::SCENARIO_USER],
			[['leadUserId'], 'integer'],
		];
	}

	public function getEventsData(bool $validate = true): array {
		if ($validate && !$this->validate()) {
			Yii::warning($this->getErrors(), __METHOD__);
			return [
				'errors' => $this->getErrors(),
			];
		}
		$query = $this->getQuery();
		$data = [];
		foreach ($query->all() as $model) {
			$event = $this->createEvent();
			$event->setModel($model);
			$data[] = $event->toArray();
		}
		return $data;
	}

	private function getQuery(): LeadQuery {
		$query = Lead::find();
		$statusesIds = array_keys(static::getStatuses());
		if (empty($statusesIds)) {
			$query->andWhere('0=1');
			return $query;
		}

		if (!empty($this->leadUserId)) {
			$query->user($this->leadUserId);
		}

		$query->joinWith([
			'status' => function (ActiveQuery $query) {
				$query->andOnCondition(LeadStatus::tableName() . '.hours_deadline IS NOT NULL');
			},
		])->joinWith(['reports']);

		$reportDateWithInterval = static::dateWithStatusDaysDeadlineInterval(true);
		$dateColumnWithInterval = static::dateWithStatusDaysDeadlineInterval(false);

		$query->andWhere([
			'and',
			[
				'=',
				LeadReport::tableName() . '.id',
				LeadReport::find()
					->select('MAX(' . LeadReport::tableName() . '.id)')
					->andWhere(LeadReport::tableName() . '.lead_id = ' . Lead::tableName() . '.id'),
			],
			['>=', $reportDateWithInterval, $this->startAt],
			['<=', $reportDateWithInterval, $this->endAt],
		]);

		$query->orWhere([
			'and',
			LeadReport::tableName() . '.lead_id IS NULL',
			['>=', $dateColumnWithInterval, $this->startAt],
			['<=', $dateColumnWithInterval, $this->endAt],
		]);
		$query->orWhere([
			'and',
			['>=', Lead::tableName() . '.deadline_at', $this->startAt],
			['<=', Lead::tableName() . '.deadline_at', $this->endAt],
		]);

		$query->groupBy(Lead::tableName() . '.id');
		return $query;
	}

	public static function getStatuses(): array {
		return LeadStatus::find()
			->andWhere(LeadStatus::tableName() . '.hours_deadline IS NOT NULL')
			->indexBy('id')
			->all();
	}

	public static function dateWithStatusDaysDeadlineInterval(bool $fromReport): string {
		$dateColumn = $fromReport
			? LeadReport::tableName() . '.created_at'
			: Lead::tableName() . '.date_at';
		$deadlineDaysColumn = LeadStatus::tableName() . '.hours_deadline';

		return "DATE_ADD( $dateColumn, INTERVAL $deadlineDaysColumn hour)";
	}

	/**
	 * @throws InvalidConfigException
	 */
	protected function createEvent(): LeadStatusDeadlineEvent {
		$event = $this->event;
		if (!isset($event['class'])) {
			$event['class'] = LeadStatusDeadlineEvent::class;
		}
		return Yii::createObject($event);
	}
}
