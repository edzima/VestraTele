<?php

namespace common\modules\lead\entities;

use common\modules\lead\models\ActiveLead;
use Yii;
use yii\base\InvalidArgumentException;

class LeadDialerEntity extends Dialer {

	private ActiveLead $lead;
	public int $connectionReportStatus;
	public bool $withSameContacts = true;

	public function __construct(ActiveLead $lead,
		$config = []) {
		$this->lead = $lead;
		parent::__construct($config);
	}

	public function init() {
		parent::init();
		if (empty($this->lead->getPhone())) {
			throw new InvalidArgumentException('Lead must has Phone.');
		}
		if (empty($this->lead->getSource()->getDialerPhone())) {
			throw new InvalidArgumentException('Lead Source must has Dialer Phone.');
		}
	}

	public function getID(): string {
		//@todo maybe LeadDialer::id
		return $this->lead->getId();
	}

	public function getDestination(): string {
		return $this->lead->getSource()->getDialerPhone();
	}

	public function getOrigin(): string {
		return $this->parsePhone($this->lead->getPhone());
	}

	public function getConnectionAttempts(): array {
		$reports = $this->lead->reports;
		if (empty($reports) && $this->withSameContacts) {
			return [];
		}
		$attempts = [];
		foreach ($reports as $report) {
			if ($report->status_id === $this->connectionReportStatus) {
				$attempts[] = strtotime($report->created_at);
			}
		}
		return $attempts;
	}

	public function getSameContactsConnectionAttempts(bool $onlySameType = true): array {
		$attempts = [];
		foreach ($this->lead->getSameContacts($onlySameType) as $lead) {
			try {
				$entity = new static($lead, [
					'connectionReportStatus' => $this->connectionReportStatus,
				]);
				$attempts[] = $entity->getConnectionAttempts();
			} catch (InvalidArgumentException $exception) {
				Yii::debug($exception->getMessage(), 'lead.dialer.entity.sameContacts');
			}
		}
		return array_merge([], ...$attempts);
	}
}
