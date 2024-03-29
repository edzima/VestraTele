<?php

namespace common\modules\lead\entities;

interface DialerInterface {

	public function getID(): string;

	public function getDestination(): string;

	public function getOrigin(): string;

	public function getDID(): string;

	public function getStatusId(): int;

	public function updateStatus(int $status): void;

	/**
	 * @return int[] Connection Attempts timestamp
	 */
	public function getConnectionAttempts(): array;

	public function shouldCall(): bool;

}
