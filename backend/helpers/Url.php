<?php

namespace backend\helpers;

use yii\helpers\BaseUrl;

class Url extends BaseUrl {

	public static function issueView(int $id): string {
		return static::to(['/issue/issue/view', 'id' => $id]);
	}

	public static function userProvisions(int $userId): string {
		return static::to(['/provision/user/user', 'id' => $userId]);
	}

}
