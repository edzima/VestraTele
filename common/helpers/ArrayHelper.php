<?php

namespace common\helpers;

use yii\helpers\BaseArrayHelper;

class ArrayHelper extends BaseArrayHelper {

	public static function toUtf8($data) {
		if (is_string($data)) {
			return StringHelper::encodeToUtf8($data);
		}

		if (is_array($data)) {
			$ret = [];
			foreach ($data as $i => $d) {
				$ret[$i] = static::toUtf8($d);
			}

			return $ret;
		}

		return $data;
	}

	public static function shortNames(array $names, int $secondLength = 1): array {
		$shortNames = [];
		foreach ($names as $index => $name) {
			$shortNames[$index] = StringHelper::shortName($name, $secondLength);
		}
		return $shortNames;
	}
}
