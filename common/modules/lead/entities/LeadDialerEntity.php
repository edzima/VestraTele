<?php

namespace common\modules\lead\entities;

use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\LeadDialer;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadStatus;
use Yii;

class LeadDialerEntity extends Dialer {

	public const NOT_CALCULATE_STATUS = [
		self::STATUS_CALLING,
	];

	const STATUS_EMPTY_LEAD_PHONE = 5;
	const STATUS_EMPTY_LEAD_SOURCE_DIALER_PHONE = 6;
	const STATUS_CURRENT_LEAD_STATUS_NOT_FOR_DIALER = 10;
	const STATUS_SAME_LEAD_STATUS_NOT_FOR_DIALER = 15;

	const STATUS_GLOBALY_LIMIT_EXCEEDED = 20;
	const STATUS_DAILY_LIMIT_EXCEEDED = 25;
	const STATUS_NEXT_CALL_INTERVAL_NOT_EXCEEDED = 30;

	const STATUS_DIALER_TYPE_INACTIVE = 40;

	public static function getStatusesNames(): array {
		return parent::getStatusesNames() + [
				static::STATUS_EMPTY_LEAD_PHONE => Yii::t('lead', 'Empty Lead Phone'),
				static::STATUS_EMPTY_LEAD_SOURCE_DIALER_PHONE => Yii::t('lead', 'Empty Lead Source Dialer Phone'),
				static::STATUS_CURRENT_LEAD_STATUS_NOT_FOR_DIALER => Yii::t('lead', 'This Lead has status not for dialer'),
				static::STATUS_SAME_LEAD_STATUS_NOT_FOR_DIALER => Yii::t('lead', 'Same Lead has status not for dialer'),
				static::STATUS_GLOBALY_LIMIT_EXCEEDED => Yii::t('lead', 'Globally attempts limit exceeded'),
				static::STATUS_DAILY_LIMIT_EXCEEDED => Yii::t('lead', 'Daily attempts limit exceeded'),
				static::STATUS_NEXT_CALL_INTERVAL_NOT_EXCEEDED => Yii::t('lead', 'Next call interval not exceeded'),
				static::STATUS_DIALER_TYPE_INACTIVE => Yii::t('lead', 'Dialer Type inactive'),
			];
	}

	public int $callingReportStatus = self::STATUS_CALLING;
	public bool $withSameContacts = true;
	public bool $withSameContactsTypeStrict = true;

	private LeadDialer $dialer;
	private ActiveLead $lead;
	private ?int $status = null;

	/**
	 * @throw InvalidArgumentException
	 */
	public function __construct(LeadDialer $leadDialer,
		$config = []) {
		$this->dialer = $leadDialer;
		if (!isset($config['lead'])) {
			$this->setLead($leadDialer->lead);
		}
		parent::__construct($config);
	}

	protected function getLead(): ActiveLead {
		return $this->lead;
	}

	protected function setLead(ActiveLead $lead): void {
		$this->lead = $lead;
	}

	public function getID(): string {
		return $this->dialer->id;
	}

	public function getDestination(): string {
		if (!empty($this->dialer->destination)) {
			return (string) $this->dialer->destination;
		}
		return (string) $this->getLead()->getSource()->getDialerPhone();
	}

	public function getOrigin(): string {
		return $this->parsePhone($this->getLead()->getPhone());
	}

	public function getDID(): string {
		return (string) $this->dialer->type->did;
	}

	public function updateStatus(int $status): void {
		switch ($status) {
			case static::STATUS_CALLING:
				$this->onCallingStatus();
				break;
			case static::STATUS_ESTABLISHED:
				$this->onEstablish();
				break;
			case static::STATUS_UNESTABLISHED:
				$this->onNotEstablish();
				break;
		}
		$this->status = $status;
		$this->dialer->last_at = date(DATE_ATOM);
		$this->dialer->status = $status;
		$this->dialer->save(false);
	}

	protected function onCallingStatus(): void {
		$this->report($this->callingReportStatus);
	}

	protected function onEstablish() {
		$this->report(static::STATUS_ESTABLISHED);
	}

	protected function onNotEstablish() {
		$this->report(static::STATUS_UNESTABLISHED);
	}

	protected function report(int $status): void {
		$report = new LeadReport();
		$report->lead_id = $this->getLead()->getId();
		$report->old_status_id = $this->getLead()->getStatusId();
		$report->owner_id = $this->dialer->type->user_id;
		$report->status_id = $status;
		$report->save(false);
	}

	public function getStatusId(bool $refresh = false): int {
		if ($this->status === null || $refresh) {
			$this->status = $this->calculateStatus();
		}
		return $this->status;
	}

	public function getConnectionAttempts(bool $refresh = false): array {
		$reports = $this->getLead()->reports;
		if (empty($reports) && !$this->withSameContacts) {
			return [];
		}
		$attempts = [];
		foreach ($reports as $report) {
			if ($report->status_id === $this->callingReportStatus) {
				$attempts[] = strtotime($report->created_at);
			}
		}
		if ($this->withSameContacts) {
			$attempts = array_merge($attempts, $this->getSameContactsConnectionAttempts());
		}
		return $attempts;
	}

	public function getSameContactsConnectionAttempts(): array {
		if (!$this->withSameContacts) {
			return [];
		}
		$attempts = [];
		foreach ($this->getLead()->getSameContacts($this->withSameContactsTypeStrict) as $lead) {
			$entity = new static($this->dialer, [
				'lead' => $lead,
			]);
			$entity->withSameContacts = false;
			$entity->callingReportStatus = $this->callingReportStatus;
			$attempts[] = $entity->getConnectionAttempts();
		}
		return array_merge([], ...$attempts);
	}

	protected function calculateStatus(): int {
		if ($this->dialerStatusNotCalculate()) {
			return $this->dialer->status;
		}
		if (empty($this->lead->getPhone())) {
			return static::STATUS_EMPTY_LEAD_PHONE;
		}
		if (!$this->dialer->type->isActive()) {
			return static::STATUS_DIALER_TYPE_INACTIVE;
		}
		if ($this->isNotForDialerLeadStatus($this->getLead()->getStatusId())) {
			return static::STATUS_CURRENT_LEAD_STATUS_NOT_FOR_DIALER;
		}
		if (empty($this->getDestination())) {
			return static::STATUS_EMPTY_LEAD_SOURCE_DIALER_PHONE;
		}
		if ($this->hasSameLeadWithStatusNotForDialer()) {
			return static::STATUS_SAME_LEAD_STATUS_NOT_FOR_DIALER;
		}
		$attempts = $this->getConnectionAttempts();
		if (!empty($attempts)) {
			if ($this->globallyLimitExceed($attempts)) {
				return static::STATUS_GLOBALY_LIMIT_EXCEEDED;
			}
			if ($this->dailyLimitExceed($attempts)) {
				return static::STATUS_DAILY_LIMIT_EXCEEDED;
			}

			if ($this->nextCallIntervalNotExceed($attempts)) {
				return static::STATUS_NEXT_CALL_INTERVAL_NOT_EXCEEDED;
			}
		}

		return static::STATUS_SHOULD_CALL;
	}

	private function globallyLimitExceed(array $attempts): bool {
		return $this->getDialerConfig()->getGloballyAttemptsLimit()
			&& count($attempts) > $this->getDialerConfig()->getGloballyAttemptsLimit();
	}

	private function dailyLimitExceed(array $attempts): bool {
		return $this->getDialerConfig()->getDailyAttemptsLimit()
			&& $this->dailyAttemptsCount($attempts) > $this->getDialerConfig()->getDailyAttemptsLimit();
	}

	private function nextCallIntervalNotExceed(array $attempts): bool {
		if ($this->getDialerConfig()->getNextCallInterval()) {
			$lastTry = max($attempts);
			if ($this->dialer->last_at && $this->dialer->last_at > $lastTry) {
				$lastTry = $this->dialer->last_at;
			}
			if ($lastTry > (time() - $this->getDialerConfig()->getNextCallInterval())) {
				return static::STATUS_NEXT_CALL_INTERVAL_NOT_EXCEEDED;
			}
		}
		return false;
	}

	protected function hasSameLeadWithStatusNotForDialer(): bool {
		if ($this->withSameContacts) {
			foreach ($this->getLead()->getSameContacts($this->withSameContactsTypeStrict) as $lead) {
				if ($this->isNotForDialerLeadStatus($lead->getStatusId())) {
					return true;
				}
			}
		}

		return false;
	}

	protected function dailyAttemptsCount(array $attempts): int {
		$today = date('Y-m-d');
		$todayCounts = 0;
		rsort($attempts);
		foreach ($attempts as $time) {
			if ($today === date('Y-m-d', $time)) {
				$todayCounts++;
			}
		}
		return $todayCounts;
	}

	protected function getDialerConfig(): DialerConfigInterface {
		return $this->dialer->getConfig();
	}

	protected function isNotForDialerLeadStatus(int $id): bool {
		return LeadStatus::notForDialer($id);
	}

	private function dialerStatusNotCalculate(): bool {
		return in_array($this->dialer->status, static::NOT_CALCULATE_STATUS, true);
	}

}
