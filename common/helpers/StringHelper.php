<?php

namespace common\helpers;

use yii\helpers\BaseStringHelper;

class StringHelper extends BaseStringHelper {

	public static function between(string $string, string $start, string $end): ?string {
		$string = ' ' . $string;
		$ini = strpos($string, $start);
		if ($ini === false) {
			return null;
		}
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		$between = substr($string, $ini, $len);
		return $between ?: null;
	}

}
