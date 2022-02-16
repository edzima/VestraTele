<?php

namespace common\modules\lead\components;

use common\modules\lead\events\LeadDialerEvent;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadStatusInterface;
use common\modules\lead\models\LeadUser;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

class LeadDialerManager extends Component {

	public const EVENT_REPORT_CALLING = 'reportCalling';
	public const EVENT_REPORT_ANSWER = 'reportAnswer';
	public const EVENT_REPORT_NOT_ANSWER = 'reportNotAnswer';
	public const EVENT_REPORT_CALLING_EXCEEDED_LIMIT = 'reportCallingExceededLimit';

	public $userId;
	public int $callingStatus;
	public int $notAnsweredStatus;
	public int $answeredStatus;
	public int $callingExceededLimitStatus;
	public ?int $callingTryDayLimit = 3;
	public ?int $callingTryGlobalLimit = 10;
	public int $nextCallInterval = 1200;
	public bool $withNewWithoutUser = false;

	public function init() {
		parent::init();
		if ($this->userId === null) {
			$this->userId = Yii::$app->user->getId();
		}
		if ($this->userId === null) {
			throw new InvalidConfigException('$userId must be set or User must be logged.');
		}
	}

	public function calling(ActiveLead $lead = null): ?array {
		$model = $lead ?: $this->findToCall();
		if ($model === null
			|| !$this->leadShouldCall($model)
			|| !$this->report($model, $this->callingStatus)
		) {
			return null;
		}
		return [
			'id' => $model->getId(),
			'destination' => $model->getSource()->getDialerPhone(),
			'phone' => $this->parsePhone($model->getPhone()),
		];
	}

	protected function parsePhone(string $phone): string {
		return str_replace([' ', '+'], ['', '00'], $phone);
	}

	public function answer(int $id): bool {
		$model = $this->findById($id);
		if ($model === null
			|| $model->getStatusId() !== $this->callingStatus) {
			return false;
		}

		return $this->report($model, $this->answeredStatus);
	}

	public function notAnswer(int $id): bool {
		$model = $this->findById($id);
		if ($model === null
			|| $model->getStatusId() !== $this->callingStatus) {
			return false;
		}
		return $this->report($model, $this->notAnsweredStatus);
	}

	private function report(ActiveLead $lead, int $status): bool {
		$report = new LeadReport();
		$report->lead_id = $lead->getId();
		$report->old_status_id = $lead->getStatusId();
		$report->status_id = $status;
		$report->owner_id = $this->userId;

		$success = $report->save();

		if (!$success) {
			Yii::error([
				'reportErrors' => $report->getErrors(),
				'attributes' => $report->getAttributes(),
			], 'lead.dialer');
		}
		$lead->updateStatus($status);
		if (!isset($lead->getUsers()[LeadUser::TYPE_DIALER])) {
			$lead->linkUser(LeadUser::TYPE_DIALER, $this->userId);
		}
		$this->triggerReport($this->getReportEventName($status), $lead, $success);
		return $success;
	}

	protected function triggerReport(string $eventName, ActiveLead $lead, bool $reported): void {
		$event = new LeadDialerEvent($lead, [
			'reported' => $reported,
		]);
		$this->trigger($eventName, $event);
	}

	/**
	 * @param int $status
	 * @return string
	 * @throws InvalidConfigException
	 */
	protected function getReportEventName(int $status): string {
		switch ($status) {
			case $this->callingStatus:
				return static::EVENT_REPORT_CALLING;
			case $this->notAnsweredStatus:
				return static::EVENT_REPORT_NOT_ANSWER;
			case $this->answeredStatus:
				return static::EVENT_REPORT_ANSWER;
			case $this->callingExceededLimitStatus:
				return static::EVENT_REPORT_CALLING_EXCEEDED_LIMIT;
		}
		throw new InvalidConfigException('Invalid $status for report Event.');
	}

	public function findToCall(): ?ActiveLead {
		$model = $this->withNewWithoutUser ? $this->findNewLeadWithoutUsers() : null;
		if ($model) {
			Yii::debug('Find new Lead Without User. ID: ' . $model->getId(), 'lead.dialer');
			return $model;
		}
		$model = $this->findNotAnsweredLead();
		if ($model) {
			Yii::debug('Find dialer User not Answered Lead. ID: ' . $model->getId(), 'lead.dialer');
			return $model;
		}
		Yii::debug('Not Found Lead for User: ' . $this->userId . '.', 'lead.dialer');

		return null;
	}

	public function findNewLeadWithoutUsers(): ?ActiveLead {
		return Lead::find()
			->withoutUsers()
			->andWhere([Lead::tableName() . '.status_id' => LeadStatusInterface::STATUS_NEW])
			->andWhere(Lead::tableName() . '.phone IS NOT NULL')
			->joinWith('leadSource')
			->andWhere(LeadSource::tableName() . '.dialer_phone IS NOT NULL')
			->orderBy(['date_at' => SORT_DESC])
			->one();
	}

	protected function findById(int $id): ?ActiveLead {
		return Lead::find()->andWhere(['id' => $id])->one();
	}

	public function findNotAnsweredLead(): ?ActiveLead {
		$query = $this->notAnsweredLeadsQuery();
		$i = 0;
		foreach ($query->batch(10) as $rows) {
			$i++;
			Yii::debug("Not Answered Batch Query: $i.", 'lead.dialer');
			foreach ($rows as $lead) {
				/** @var $lead Lead */
				if ($this->leadShouldCall($lead)) {
					return $lead;
				}
			}
		}
		return null;
	}

	public function leadShouldCall(ActiveLead $lead): bool {
		Yii::debug('Check Lead should call. ID: ' . $lead->getId());
		if (empty($lead->getPhone())) {
			Yii::debug("Lead without Phone.", 'lead.dialer');
			return false;
		}
		if (empty($lead->getSource()->getDialerPhone())) {
			Yii::debug("Lead with Source without Dialer Phone.", 'lead.dialer');
			return false;
		}
		if ($this->callingTryGlobalLimit <= 0) {
			Yii::debug("Lead with Calling Reports without global calling limit.", 'lead.dialer');
			return true;
		}
		$reports = $lead->reports;
		if (empty($reports)) {
			Yii::debug("Lead without Reports.", 'lead.dialer');
			return true;
		}
		$callingReports = [];
		foreach ($reports as $report) {
			if ($report->status_id === $this->callingStatus) {
				$callingReports[] = strtotime($report->created_at);
			}
		}
		if (empty($callingReports)) {
			Yii::debug("Lead without calling Reports.", 'lead.dialer');
			return true;
		}

		if ($this->isExceededGlobalLimit(count($callingReports))) {
			Yii::warning("Lead overwrite calling limit.", 'lead.dialer');
			$this->report($lead, $this->callingExceededLimitStatus);
			return false;
		}

		if ($this->callingTryDayLimit <= 0) {
			Yii::debug("Lead with Calling Reports without day calling limit.", 'lead.dialer');
			return true;
		}

		$lastTry = max($callingReports);
		if ($this->shouldCallForTime($lastTry)) {
			$today = date('Y-m-d');
			$todayCounts = 0;
			rsort($callingReports);
			foreach ($callingReports as $time) {
				if ($today === date('Y-m-d', $time)) {
					$todayCounts++;
				}
			}
			Yii::debug('Lead was today: ' . $todayCounts . ' calling tries.');
			return $todayCounts < $this->callingTryDayLimit;
		}
		Yii::debug('Lead was (all time): ' . count($callingReports) . ' calling tries.');
		return false;
	}

	protected function isExceededGlobalLimit(int $callingTries): bool {
		return $this->callingTryGlobalLimit !== null && $callingTries > $this->callingTryGlobalLimit;
	}

	protected function shouldCallForTime(int $lastTry): bool {
		if ($this->nextCallInterval <= 0) {
			return true;
		}
		return $lastTry < (time() - $this->nextCallInterval);
	}

	public function notAnsweredLeadsQuery(): ActiveQuery {
		return Lead::find()
			->select([
				Lead::tableName() . '.*',
				'MAX(lead_report.created_at) as maxCreatedAt',
			])
			->dialer($this->userId)
			->andWhere([Lead::tableName() . '.status_id' => $this->notAnsweredStatus])
			->andWhere(Lead::tableName() . '.phone IS NOT NULL')
			->joinWith('leadSource')
			->andWhere(LeadSource::tableName() . '.dialer_phone IS NOT NULL')
			->groupBy([
				Lead::tableName() . '.phone',
				//@todo check report dates
				//			LeadReport::tableName() . '.lead_id',
			])
			->joinWith([
				'reports' => function (ActiveQuery $query): void {
					$query->orderBy([]);
				},
			])
			->orderBy([
				'maxCreatedAt' => SORT_ASC,
			]);
	}

}
