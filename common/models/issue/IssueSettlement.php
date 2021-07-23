<?php

namespace common\models\issue;

use common\models\settlement\SettlementInterface;

/**
 * Interface IssueSettlement
 *
 * @property-read IssuePay[] $pays
 */
interface IssueSettlement extends IssueInterface, SettlementInterface {

}
