<?php

namespace common\modules\lead\models;

use DateTime;

interface LeadInterface {

	public function getSource(): LeadSourceInterface;

	public function getStatusId(): int;

	public function getDateTime(): DateTime;

	public function getData(): array;

	public function getProvider(): ?string;

	public function getPhone(): ?string;

	public function getEmail(): ?string;

	public function getPostalCode(): ?string;

	/**
	 * @return int[] users IDs indexed by type.
	 */
	public function getUsers(): array;

	public function getCampaignId(): ?int;

	public function setLead(LeadInterface $lead): void;


}
