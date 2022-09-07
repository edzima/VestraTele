<?php

namespace common\modules\lead\entities;

interface DialerConfigInterface {

	public function getDailyAttemptsLimit(): ?int;

	public function getGloballyAttemptsLimit(): ?int;

	public function getNextCallInterval(): int;
}
