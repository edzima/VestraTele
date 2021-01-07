<?php

namespace backend\tests\Step\Functional;

use common\models\user\User;

/**
 * Class Bookkeeper
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class Bookkeeper extends Manager {

	protected function getUsername(): string {
		return 'bookkeeper';
	}

	protected function getRoles(): array {
		return array_merge(parent::getRoles(), [
			User::ROLE_BOOKKEEPER,
		]);
	}

}
