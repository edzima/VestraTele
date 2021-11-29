<?php

namespace backend\helpers;

use common\helpers\Url as BaseUrl;

/**
 * Url helper for backend app.
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class Url extends BaseUrl {

	public static function userProvisions(int $userId, int $typeId = null): string {
		return static::to(['/provision/user/user-view', 'userId' => $userId, 'typeId' => $typeId]);
	}

	protected static function managerConfig(): array {
		return require __DIR__ . '/../config/_urlManager.php';
	}

}
