<?php

namespace common\modules\credit\components;

interface InterestRateInterface {

	public function getInterestRate(string $date): ?float;
}
