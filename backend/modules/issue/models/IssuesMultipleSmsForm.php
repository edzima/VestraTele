<?php

namespace backend\modules\issue\models;

use common\models\issue\IssueUser;
use common\models\message\IssuesSmsMultipleForm as BaseIssuesMultipleSmsForm;

class IssuesMultipleSmsForm extends BaseIssuesMultipleSmsForm {

	public array $userTypes = [
		IssueUser::TYPE_CUSTOMER => IssueUser::TYPE_CUSTOMER,
	];
	protected const ISSUE_SMS_CLASS = IssueSmsForm::class;

}
