<?php

namespace frontend\widgets\issue;

use common\modules\issue\widgets\IssueSmsButtonDropdown as BaseIssueSmsButtonDropdown;

class IssueSmsButtonDropdown extends BaseIssueSmsButtonDropdown {

	public string $route = '/issue-sms/push';
}
