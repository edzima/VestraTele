<?php

namespace frontend\tests\functional\lead;

class DialerTypeCest extends NotAllowedLeadCest {

	protected function routes(): array {
		return [
			['/lead/dialer-type/index'],
			['/lead/dialer-type/create'],
			['/lead/dialer-type/view'],
			['/lead/dialer-type/update'],
		];
	}
}
