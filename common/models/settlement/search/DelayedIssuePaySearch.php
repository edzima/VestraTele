<?php

namespace common\models\settlement\search;

class DelayedIssuePaySearch extends IssuePaySearch {

	public string $payStatus = self::PAY_STATUS_NOT_PAYED;

	public function rules(): array {
		return array_merge(
			[
				['delay', 'required'],
			],
			parent::rules()
		);
	}

	public static function getPayStatusNames(): array {
		$names = parent::getPayStatusNames();
		unset($names[static::PAY_STATUS_ALL], $names[static::PAY_STATUS_PAYED]);
		return $names;
	}

	public static function getDelaysRangesNames(): array {
		$names = parent::getDelaysRangesNames();
		unset($names[static::DELAY_NONE]);
		return $names;
	}

}
