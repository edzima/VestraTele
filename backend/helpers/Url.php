<?php

namespace backend\helpers;

use common\helpers\Url as BaseUrl;

/**
 * Url helper for backend app.
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class Url extends BaseUrl {

	public static function userProvisions(int $userId): string {
		return static::to(['/provision/user/user', 'id' => $userId]);
	}

}
