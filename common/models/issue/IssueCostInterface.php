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

	public const TYPE_COURT_ENTRY = 'court_entry';
	public const TYPE_POWER_OF_ATTORNEY = 'power_of_attorney';
	public const TYPE_PURCHASE_OF_RECEIVABLES = 'purchase_of_receivables';
	public const TYPE_WRIT = 'writ';
	public const TYPE_OFFICE = 'office';

	public const TYPE_CESSION = 'cession';

	public const TYPE_SHIPMENTS = 'shipments';
	public const TYPE_JUSTIFICATION_OF_THE_JUDGMENT = 'justification_of_the_judgment';
	public const TYPE_INSTALLMENT = 'installment';
	public const TYPE_ATTESTATION = 'attestation';
	public const TYPE_COMMISSION_REFUND = 'commission_refund';
	public const TYPE_PCC = 'pcc';
	public const TYPE_PIT_4 = 'PIT-4';

	public const TYPE_APPEAL = 'appeal';

	public const TYPE_COURT_EXPERT = 'court_expert';
	public const TYPE_APPEARANCE_OF_LAWYER = 'Appearance of a lawyer';

}
