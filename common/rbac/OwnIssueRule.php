<?php

namespace common\rbac;

use common\models\issue\IssueInterface;
use yii\rbac\Rule;

class OwnIssueRule extends Rule {

	/** @var string */
	public $name = 'ownIssueRule';

	public function execute($user, $item, $params) {
		/** @var $issue IssueInterface */
		$issue = $params['issue'] ?? null;
		if ($issue && $user) {
			return $issue->getIssueModel()->isForUser($user);
		}
		return false;
	}
}
