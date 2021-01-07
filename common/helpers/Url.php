<?php

namespace common\helpers;

use yii\helpers\BaseUrl;

/**
 * Base Url helper.
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class Url extends BaseUrl {

	public const ROUTE_ISSUE_VIEW = '/issue/issue/view';

	public static function issueView(int $id): string {
		return static::toRoute([static::ROUTE_ISSUE_VIEW, 'id' => $id]);
	}

}
