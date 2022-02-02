<?php

namespace common\models\query;

interface PhonableQuery {

	public function withPhoneNumber(string $phone): self;
}
