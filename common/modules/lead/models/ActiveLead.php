<?php

namespace common\modules\lead\models;

/**
 * Interface ActiveLead
 *
 * @property-read LeadUserInterface|null $owner
 * @property-read LeadReport[] $reports
 * @property-read LeadAnswer[] $answers
 * @property-read LeadAddress[] $addresses
 * @property-read LeadMarket|null $market
 */
interface ActiveLead extends LeadInterface {

	public function getId(): int;

	public function getDetails(): ?string;

	public function updateFromLead(LeadInterface $lead): void;

	public function updateStatus(int $status_id): bool;

	public function updateName(string $name): bool;

	public function unlinkUsers(): void;

	public function linkUser(string $type, int $user_id): void;

	/**
	 * @param string $id
	 * @return ?static
	 */
	public static function findById(string $id): ?self;

	public function setLead(LeadInterface $lead): void;

	/**
	 * @param LeadInterface $lead
	 * @return static[]
	 */
	public static function findByLead(LeadInterface $lead): array;

}
