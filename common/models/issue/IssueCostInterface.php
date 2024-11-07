<?php

namespace common\models\issue;

use common\models\settlement\CostInterface;
use common\models\settlement\TransferType;
use common\models\settlement\VATInfo;

/**
 * @property-read IssueSettlement[] $settlements
 */
interface IssueCostInterface extends
	CostInterface,
	TransferType,
	VATInfo {

}
