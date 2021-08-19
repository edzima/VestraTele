<?php

namespace common\models\issue;

use common\models\settlement\PayInterface;
use common\models\settlement\VATInfo;
use DateTime;

/**
 * Interface IssuePayInterface
 *
 * @package common\models\issue
 * @property-read IssueSettlement $calculation
 */
interface IssuePayInterface extends
	PayInterface,
	VATInfo {

	public function markAsPaid(DateTime $date, string $transfer_type): bool;

	public function getSettlementId(): int;

}
