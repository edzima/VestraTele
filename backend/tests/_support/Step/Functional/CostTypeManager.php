<?php

namespace backend\tests\Step\Functional;

use backend\modules\settlement\Module;

/**
 * Class CostTypeManager
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class CostTypeManager extends Manager {

	protected function getUsername(): string {
		return 'cost-type-manager';
	}

	protected function getRoles(): array {
		return array_merge(parent::getRoles(), [
			Module::ROLE_COST_TYPE_MANAGER,
		]);
	}

}
