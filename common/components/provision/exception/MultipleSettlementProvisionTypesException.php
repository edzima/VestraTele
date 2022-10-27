<?php

namespace common\components\provision\exception;

class MultipleSettlementProvisionTypesException extends Exception {

	public function getName() {
		return 'Settlement has multiple Active Provision Types';
	}
}
