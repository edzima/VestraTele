<?php

namespace backend\helpers;

use yii\helpers\BaseUrl;

class Url extends BaseUrl {

	public static function issueView(int $id): string {
		return static::to(['/issue/issue/view', 'id' => $id]);
	}

	public static function payCityDetails(int $cityId = null): string {
		return static::to(['/issue/pay-city/create', 'city_id' => $cityId]);
	}

}