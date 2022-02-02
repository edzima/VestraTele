<?php

namespace common\helpers;

use Yii;

class Flash {

	public const TYPE_WARNING = 'warning';
	public const TYPE_ERROR = 'error';
	public const TYPE_SUCCESS = 'success';
	public const TYPE_INFO = 'info';

	public static function add(string $key, $message, bool $removeAfterAccess = true): void {
		Yii::$app->session->addFlash($key, $message, $removeAfterAccess);
	}
}
