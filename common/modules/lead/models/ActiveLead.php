<?php

namespace common\modules\lead\models;

use yii\base\Arrayable;

/**
 * Interface ActiveLead
 *
 * @property-read LeadUserInterface|null $owner
 * @property-read LeadReport[] $reports
 * @property-read LeadAnswer[] $answers
 * @property-read LeadAddress[] $addresses
 * @property-read LeadMarket|null $market
 * @property-read LeadPhoneBlacklist|null $phoneBlacklist
 */
interface ActiveLead extends LeadInterface, Arrayable {

	public function isDelay(): ?bool;

	public function getDeadlineHours();

	public function getDeadline(): ?string;

	public function getId(): int;

	public function getHash(): string;

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
