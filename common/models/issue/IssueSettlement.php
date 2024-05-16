<?php

namespace common\models\issue;

use common\models\settlement\SettlementInterface;

/**
 * Interface IssueSettlement
 *
 * @property-read IssuePay[] $pays
 * @property-read IssueCost[] $costs
 */
interface IssueSettlement extends IssueInterface, SettlementInterface {

	public const TYPE_ADMINISTRATIVE = 10;
	public const TYPE_APPEAL = 15;

	public const TYPE_ENTRY_FEE = 20;
	public const TYPE_HONORARIUM = 30;
	public const TYPE_HONORARIUM_VINDICATION = 31;
	public const TYPE_LAWYER = 40;
	public const TYPE_REQUEST_FOR_JUSTIFICATION = 45;
	public const TYPE_SUBSCRIPTION = 50;
	public const TYPE_DEBT = 100;

	public const TYPE_INTEREST = 110;

	public const TYPE_COST_REFUND_SELF = 150;
	public const TYPE_COST_REFUND_LEGAL_REPRESANTION = 151;

	public const PROVIDER_CLIENT = 1;
	public const PROVIDER_RESPONSIBLE_ENTITY = 10;

	public function getId(): int;

	public function getProviderType(): int;

	public function getOwnerId(): int;

	public function getFrontendUrl(): string;

	public function getCreatedAt(): string;

}
