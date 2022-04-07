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
	public const TYPE_HONORARIUM = 30;
	public const TYPE_LAWYER = 40;
	public const TYPE_REQUEST_FOR_JUSTIFICATION = 45;
	public const TYPE_SUBSCRIPTION = 50;
	public const TYPE_DEBT = 100;

	public const PROVIDER_CLIENT = 1;
	public const PROVIDER_RESPONSIBLE_ENTITY = 10;

	public function getId(): int;

	public function getProviderType(): int;

	public function getOwnerId(): int;

	public function getFrontendUrl(): string;

}
