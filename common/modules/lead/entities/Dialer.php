<?php

namespace common\modules\lead\entities;

use yii\base\BaseObject;
use function str_replace;

abstract class Dialer extends BaseObject implements DialerInterface {

	protected function parsePhone(string $phone): string {
		return str_replace([' ', '+'], ['', '00'], $phone);
	}

}
