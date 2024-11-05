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

	public const PROVIDER_CLIENT = 1;
	public const PROVIDER_RESPONSIBLE_ENTITY = 10;

	public function getId(): int;

	public function getProviderType(): int;

	public function getOwnerId(): int;

	public function getFrontendUrl(): string;

	public function getCreatedAt(): string;

}
