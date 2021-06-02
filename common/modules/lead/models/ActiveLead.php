<?php

namespace common\modules\lead\models;

/**
 * Interface ActiveLead
 *
 * @property-read LeadReportInterface[] $reports
 * @property-read LeadAnswer[] $answers
 * @property-read LeadAddress[] $addresses
 */
interface ActiveLead extends LeadInterface {

	public function getId(): string;

	public function updateFromLead(LeadInterface $lead): void;

	public function updateStatus(int $status_id): bool;

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

	public function isForUser($id): bool;

}
