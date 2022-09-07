<?php

namespace frontend\tests\functional\lead;

class StatusCest extends NotAllowedLeadCest {

	protected function routes(): array {
		return [
			['/lead/status/index'],
			['/lead/status/create'],
			['/lead/status/view'],
			['/lead/status/update'],
		];
	}

}
