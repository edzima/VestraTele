<?php

namespace common\modules\lead\components;

use common\modules\lead\events\LeadDialerEvent;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadStatusInterface;
use common\modules\lead\models\LeadUser;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

class LeadDialer extends Component {

	public const EVENT_REPORT_CALLING = 'reportCalling';
	public const EVENT_REPORT_ANSWER = 'reportAnswer';
	public const EVENT_REPORT_NOT_ANSWER = 'reportNotAnswer';

	public $userId;
	public int $callingStatus;
	public int $notAnsweredStatus;
	public int $answeredStatus;
	public int $notAnsweredLimit = 3;

	public function init() {
		parent::init();
		if ($this->userId === null) {
			$this->userId = Yii::$app->user->getId();
		}
		if ($this->userId === null) {
			throw new InvalidConfigException('$userId must be set or User must be logged.');
		}
	}

	public function calling(): ?array {
		$model = $this->findToCall();
		if ($model === null
			|| empty($model->getPhone())
			|| !$this->report($model, $this->callingStatus)
		) {
			return null;
		}
		$phone = str_replace([' ', '+'], ['', '00'], $model->getPhone());
		return [
			'id' => $model->getId(),
			'phone' => $phone,
		];
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

		$success = $report->save() && $lead->updateStatus($status);
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
		}
		throw new InvalidConfigException('Invalid $status for report Event.');
	}

	public function findToCall(): ?ActiveLead {
		$model = $this->findNewLeadWithoutUsers();
		if ($model) {
			return $model;
		}
		$model = $this->findNotAnsweredLead();
		if ($model) {
			return $model;
		}
		return null;
	}

	public function findNewLeadWithoutUsers(): ?ActiveLead {
		return Lead::find()
			->withoutUsers()
			->orderBy(['date_at' => SORT_DESC])
			->andWhere('phone IS NOT NULL')
			->andWhere(['status_id' => LeadStatusInterface::STATUS_NEW])
			->one();
	}

	protected function findById(int $id): ?ActiveLead {
		return Lead::find()->andWhere(['id' => $id])->one();
	}

	private function findNotAnsweredLead(): ?ActiveLead {
		$models = Lead::find()
			->user($this->userId)
			->andWhere([Lead::tableName() . '.status_id' => $this->notAnsweredStatus])
			->joinWith('reports')
			->all();

		shuffle($models);

		foreach ($models as $model) {
			$notAnsweredReports = 0;
			foreach ($model->reports as $report) {
				if ($report->status_id === $this->notAnsweredStatus) {
					$notAnsweredReports++;
				}
			}
			if ($notAnsweredReports < $this->notAnsweredLimit) {
				return $model;
			}
		}
		return null;
	}

}
