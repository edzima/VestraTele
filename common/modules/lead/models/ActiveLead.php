<?php

namespace common\modules\lead\models;

interface ActiveLead extends LeadInterface {

	public function getId(): string;

	public function updateFromLead(LeadInterface $lead): void;

	public function updateStatus(int $status_id): bool;

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
