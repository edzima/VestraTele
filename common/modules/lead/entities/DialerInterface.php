<?php

namespace common\modules\lead\entities;

interface DialerInterface {

	public function getID(): string;

	public function getDestination(): string;

	public function getOrigin(): string;

	/**
	 * @return int[] Connection Attempts timestamp
	 */
	public function getConnectionAttempts(): array;

}
