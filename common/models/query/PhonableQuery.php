<?php

namespace common\models\query;

interface PhonableQuery {

	public function withPhoneNumber($phone): self;
}
