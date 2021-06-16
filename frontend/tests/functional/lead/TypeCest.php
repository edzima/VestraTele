<?php

namespace frontend\tests\functional\lead;

class TypeCest extends NotAllowedLeadCest {

	protected function routes(): array {
		return [
			['/lead/type/index'],
			['/lead/type/create'],
			['/lead/type/view'],
			['/lead/type/update'],
		];
	}

}
