<?php

namespace common\models\message;

use common\models\issue\IssueUser;
use common\modules\court\models\Lawsuit;
use yii\base\InvalidArgumentException;

class IssueLawsuitSmsForm extends IssueSmsForm {

	public array $userTypes = [
		IssueUser::TYPE_CUSTOMER,
	];

	protected static function mainKeys(): array {
		return [
			'issue',
			'lawsuit',
		];
	}

	protected Lawsuit $lawsuit;

	public function setLawsuit(Lawsuit $lawsuit): void {
		$this->lawsuit = $lawsuit;
		$issues = $lawsuit->issues;
		if (empty($issues)) {
			throw new InvalidArgumentException('Lawsuit: ' . $lawsuit->id . ' must has issues.', __METHOD__);
		}
	}

}
